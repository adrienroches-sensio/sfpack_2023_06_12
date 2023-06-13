<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Movie as MovieEntity;
use DateTimeImmutable;
use function array_map;

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
            genres: [], // TODO : use doctrine relations
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

    public function year(): string
    {
        return $this->releasedAt->format('Y');
    }
}
