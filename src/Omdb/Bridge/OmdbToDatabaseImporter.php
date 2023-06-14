<?php

declare(strict_types=1);

namespace App\Omdb\Bridge;

use App\Entity\Genre as GenreEntity;
use App\Entity\Movie as MovieEntity;
use App\Model\Rated;
use App\Omdb\Api\Movie;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use DateTimeImmutable;
use function explode;
use function sprintf;
use function urlencode;

final class OmdbToDatabaseImporter implements OmdbToDatabaseImporterInterface
{
    public function __construct(
        private readonly MovieRepository $movieRepository,
        private readonly GenreRepository $genreRepository,
    ) {
    }

    public function importFromApiData(Movie $movie, bool $flush = false): MovieEntity
    {
        $newMovie = (new MovieEntity())
            ->setTitle($movie->Title)
            ->setPoster($movie->Poster)
            ->setRated(Rated::tryFrom($movie->Rated) ?? Rated::GeneralAudiences)
            ->setPlot($movie->Plot)
            ->setReleasedAt(new DateTimeImmutable($movie->Released))
            ->setSlug(sprintf('%s-%s', $movie->Year, urlencode($movie->Title)))
            ->setGenres($this->getGenres($movie->Genre))
        ;

        $this->movieRepository->save($newMovie, $flush);

        return $newMovie;
    }

    /**
     * @return list<GenreEntity>
     */
    private function getGenres(string $genres): array
    {
        $result = [];

        foreach (explode(', ', $genres) as $genreName) {
            $result[] = $this->genreRepository->get($genreName);
        }

        return $result;
    }
}
