<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

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

    public function year(): string
    {
        return $this->releasedAt->format('Y');
    }
}
