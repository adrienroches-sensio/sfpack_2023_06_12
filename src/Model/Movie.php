<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Genre as GenreEntity;
use App\Entity\Movie as MovieEntity;
use App\Omdb\Api\Movie as MovieOmdb;
use DateTimeImmutable;
use function array_map;
use function explode;
use function str_starts_with;

final class Movie
{
    /**
     * @param list<string> $genres
     */
    public function __construct(
        public readonly string            $slug,
        public readonly string            $title,
        public readonly string            $plot,
        public readonly DateTimeImmutable $releasedAt,
        public readonly string            $poster,
        public readonly array             $genres,
        public readonly Rated             $rated,
    ) {
    }

    public static function fromMovieEntity(MovieEntity $movieEntity): self
    {
        return new self(
            slug: $movieEntity->getSlug(),
            title: $movieEntity->getTitle(),
            plot: $movieEntity->getPlot(),
            releasedAt: $movieEntity->getReleasedAt(),
            poster: $movieEntity->getPoster(),
            genres: $movieEntity->getGenres()->map(static function (GenreEntity $genreEntity): string {
                return $genreEntity->getName();
            })->toArray(),
            rated: $movieEntity->getRated(),
        );
    }

    /**
     * @param list<MovieEntity> $movieEntities
     *
     * @return list<self>
     */
    public static function fromMovieEntities(array $movieEntities): array
    {
        return array_map(self::fromMovieEntity(...), $movieEntities);
    }

    public static function fromOmdb(MovieOmdb $movieOmdb): self
    {
        return new self(
            slug: $movieOmdb->Title,
            title: $movieOmdb->Title,
            plot: $movieOmdb->Plot,
            releasedAt: new DateTimeImmutable($movieOmdb->Released),
            poster: $movieOmdb->Poster,
            genres: explode(', ', $movieOmdb->Genre),
            rated: Rated::tryFrom($movieOmdb->Rated) ?? Rated::GeneralAudiences,
        );
    }

    public function year(): string
    {
        return $this->releasedAt->format('Y');
    }

    public function isRemotePoster(): bool
    {
        return str_starts_with($this->poster, 'http');
    }
}
