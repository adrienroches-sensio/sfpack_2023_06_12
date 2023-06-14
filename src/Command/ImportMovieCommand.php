<?php

namespace App\Command;

use App\Entity\Movie as MovieEntity;
use App\Omdb\Api\NoResult;
use App\Omdb\Api\OmdbApiClientInterface;
use App\Omdb\Api\SearchResult;
use App\Omdb\Bridge\OmdbToDatabaseImporterInterface;
use App\Repository\MovieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use function array_key_exists;
use function array_key_first;
use function array_reduce;
use function count;
use function sprintf;

#[AsCommand(
    name: 'app:import:movie',
    description: 'Import one or more movies from the OMDB API into your database',
    aliases: ['omdb:import:movie', 'import:movie']
)]
class ImportMovieCommand extends Command
{
    public function __construct(
        private readonly MovieRepository                 $movieRepository,
        private readonly OmdbApiClientInterface          $omdbApiClient,
        private readonly OmdbToDatabaseImporterInterface $omdbToDatabaseImporter,
    ) {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id-or-title', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Can either be a valid IMDB ID or a Title to search for.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Won\'t import movies to database. Only display summary of actions.')
            ->setHelp(<<<'EOT'
                The <info>%command.name%</info> import movies data from OMDB API to database :
                Using only titles
                    <info>php %command.full_name% "movie1-title" "movie2-title" ...</info>
                Using only IMDB ID's
                    <info>php %command.full_name% "id1" "id2" ...</info>
                Or mixing both
                    <info>php %command.full_name% "movie1-title" "id2" ...</info>
                EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('OMDB Import');

        /** @var list<string> $idOrTitleList */
        $idOrTitleList = $input->getArgument('id-or-title');
        $io->note(sprintf('Trying to import %d movies.', count($idOrTitleList)));

        /** @var list<array{string, MovieEntity}> $moviesImported */
        $moviesImported = [];

        /** @var list<string> $moviesFailed */
        $moviesFailed = [];

        foreach ($idOrTitleList as $idOrTitle) {
            $movie = $this->import($io, $idOrTitle);

            if (null === $movie) {
                $moviesFailed[] = $idOrTitle;
                continue;
            }

            $moviesImported[] = [$idOrTitle, $movie];
        }

        $isDryRun = $input->getOption('dry-run');
        if (false === $isDryRun) {
            $this->movieRepository->flush();
        }

        if (count($moviesImported) > 0) {
            $io->success('These were imported :');
            $io->table(
                ['ID', 'Search Query', 'Title'],
                array_reduce($moviesImported, static function (array $rows, array $movieImported): array {
                    /** @var string $idOrTitle */
                    /** @var MovieEntity $movie */
                    [$idOrTitle, $movie] = $movieImported;

                    $rows[] = [
                        $movie->getId(),
                        $idOrTitle,
                        "{$movie->getTitle()} ({$movie->getReleasedAt()->format('Y')})",
                    ];

                    return $rows;
                }, [])
            );
        }

        if (count($moviesFailed) > 0) {
            $io->error('Those search terms could not be found or were skipped.');
            $io->listing($moviesFailed);
        }

        return Command::SUCCESS;
    }

    private function import(SymfonyStyle $io, string $idOrTitle): MovieEntity|null
    {
        $io->section("'{$idOrTitle}'");

        return $this->tryImportAsImdbId($io, $idOrTitle) ?? $this->searchAndImportByTitle($io, $idOrTitle);
    }

    private function tryImportAsImdbId(SymfonyStyle $io, string $imdbId, bool $confirm = true): MovieEntity|null
    {
        try {
            $result = $this->omdbApiClient->getById($imdbId);
        } catch (NoResult) {
            return null;
        }

        $acceptImport = true;

        if (true === $confirm) {
            $acceptImport = $io->askQuestion(new ConfirmationQuestion("Do you wish to import '{$result->Title} ({$result->Year})' ?", true));
        }

        if (false === $acceptImport) {
            $io->warning('  >>> Skipping');
            return null;
        }

        return $this->omdbToDatabaseImporter->importFromApiData($result);
    }

    private function searchAndImportByTitle(SymfonyStyle $io, string $title): MovieEntity|null
    {
        try {
            $searchResults = $this->omdbApiClient->searchByTitle($title);
        } catch (NoResult) {
            return null;
        }

        $choices = array_reduce($searchResults, static function (array $choices, SearchResult $searchResult): array {
            $choices[$searchResult->imdbId] = "{$searchResult->Title} ({$searchResult->Year})";

            return $choices;
        }, []);

        if (count($choices) === 1) {
            $selectedChoice = array_key_first($choices);
        } else {
            $choices['none'] = 'None of the above';
            $selectedChoice = $io->choice('Which movie would you like to import ?', $choices);

            if ('none' === $selectedChoice) {
                $io->warning('  >>> Skipping');

                return null;
            }
        }

        return $this->tryImportAsImdbId($io, $selectedChoice, false);
    }
}
