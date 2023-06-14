<?php

declare(strict_types=1);

namespace App\Omdb\Api;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use function array_key_exists;

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
            NoResult::forId($imdbId, $throwable);
        }

        if (array_key_exists('Response', $result) === true && 'False' === $result['Response']) {
            NoResult::forId($imdbId);
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
}
