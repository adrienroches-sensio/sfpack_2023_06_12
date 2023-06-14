<?php

declare(strict_types=1);

namespace App\Omdb\Api;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use function array_key_exists;
use function array_map;
use function count;

final class OmdbApiClient implements OmdbApiClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $omdbApiClient,
    ) {
    }

    public function getById(string $imdbId): Movie
    {
        $response = $this->omdbApiClient->request('GET', '/', [
            'query' => [
                'i' => $imdbId,
                'plot' => 'full',
                'r' => 'json',
            ]
        ]);

        try {
            /** @var array{Title: string, Year: string, Rated: string, Released: string, Genre: string, Plot: string, Poster: string, imdbID: string, Type: string, Response: string} $result */
            $result = $response->toArray(true);
        } catch (Throwable $throwable) {
            throw NoResult::forId($imdbId, $throwable);
        }

        if (array_key_exists('Response', $result) === true && 'False' === $result['Response']) {
            throw NoResult::forId($imdbId);
        }

        return new Movie(
            Title: $result['Title'],
            Year: $result['Year'],
            Rated: $result['Rated'],
            Released: $result['Released'],
            Genre: $result['Genre'],
            Plot: $result['Plot'],
            Poster: $result['Poster'],
            imdbID: $result['imdbID'],
            Type: $result['Type'],
        );
    }

    public function searchByTitle(string $title): array
    {
        $response = $this->omdbApiClient->request('GET', '/', [
            'query' => [
                's' => $title,
                'r' => 'json',
                'page' => 1,
                'type' => 'movie',
            ],
        ]);

        try {
            /** @var array{Search: list<array{Title: string, Year: string, imdbID: string, Type: string, Poster: string}>, totalResults: string} $result */
            $result = $response->toArray(true);
        } catch (Throwable $throwable) {
            throw NoResult::searchingForTitle($title, $throwable);
        }

        if (array_key_exists('Response', $result) === true && 'False' === $result['Response']) {
            throw NoResult::searchingForTitle($title);
        }

        if (count($result['Search']) === 0) {
            throw NoResult::searchingForTitle($title);
        }

        return array_map(
            static function (array $rawSearchResult): SearchResult {
                return new SearchResult(
                    Title: $rawSearchResult['Title'],
                    Year: $rawSearchResult['Year'],
                    imdbId: $rawSearchResult['imdbID'],
                    Type: $rawSearchResult['Type'],
                    Poster: $rawSearchResult['Poster'],
                );
            },
            $result['Search']
        );
    }
}
