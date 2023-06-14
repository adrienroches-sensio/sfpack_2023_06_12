<?php

declare(strict_types=1);

namespace App\Omdb\Api;

final class Movie
{
    public function __construct(
        public readonly string $Title,
        public readonly string $Year,
        public readonly string $Rated,
        public readonly string $Released,
        public readonly string $Genre,
        public readonly string $Plot,
        public readonly string $Poster,
        public readonly string $imdbID,
        public readonly string $Type,
    ) {
    }
}
