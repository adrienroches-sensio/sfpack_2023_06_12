<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:movie',
    description: 'Import one or more movies from the OMDB API into your database',
    aliases: ['omdb:import:movie', 'import:movie']
)]
class ImportMovieCommand extends Command
{
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
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
