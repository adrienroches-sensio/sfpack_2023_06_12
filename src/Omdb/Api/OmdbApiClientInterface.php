<?php

declare(strict_types=1);

namespace App\Omdb\Api;

interface OmdbApiClientInterface
{
    public function getById(string $imdbId): Movie;

    /**
     * @return list<SearchResult>
     */
    public function searchByTitle(string $title): array;
}
