<?php

declare(strict_types=1);

namespace App\Omdb\Api;

final class SearchResult
{
    public function __construct(
        public readonly string $Title,
        public readonly string $Year,
        public readonly string $imdbId,
        public readonly string $Type,
        public readonly string $Poster,
    ) {
    }
}
