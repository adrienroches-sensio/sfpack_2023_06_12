<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function array_column;
use function array_map;

/**
 * @phpstan-type MovieDetails array{title: string, slug: string, releasedAt: string, plot: string, genres: list<string>}
 */
final class MovieRepository
{
    /** @var array list<MovieDetails> */
    private const MOVIES = [
        [
            'title' => 'Astérix et Obélix: Mission Cléopâtre',
            'slug' => '2002-asterix-et-obelix-mission-cleopatre',
            'releasedAt' => '30 Jan 2002',
            'plot' => "Cléopâtre, la reine d’Égypte, décide, pour défier l'Empereur romain Jules César, de construire en trois mois un palais somptueux en plein désert. Si elle y parvient, celui-ci devra concéder publiquement que le peuple égyptien est le plus grand de tous les peuples. Pour ce faire, Cléopâtre fait appel à Numérobis, un architecte d'avant-garde plein d'énergie. S'il réussit, elle le couvrira d'or. S'il échoue, elle le jettera aux crocodiles.
    Celui-ci, conscient du défi à relever, cherche de l'aide auprès de son vieil ami Panoramix. Le druide fait le voyage en Égypte avec Astérix et Obélix. De son côté, Amonbofis, l'architecte officiel de Cléopâtre, jaloux que la reine ait choisi Numérobis pour construire le palais, va tout mettre en œuvre pour faire échouer son concurrent.",
            'genres' => ['Comedy']
        ]
    ];

    /**
     * @param MovieDetails $movieDetails
     */
    private function convertToModel(array $movieDetails): Movie
    {
        return new Movie(
            slug: $movieDetails['slug'],
            title: $movieDetails['title'],
            plot: $movieDetails['plot'],
            releasedAt: new DateTimeImmutable($movieDetails['releasedAt']),
            genres: $movieDetails['genres'],
        );
    }

    public function getBySlug(string $movieSlug): Movie
    {
        $indexedBySlug = array_column(self::MOVIES, null, 'slug');

        $movieDetails = $indexedBySlug[$movieSlug] ?? throw new NotFoundHttpException('Movie slug not found');

        return $this->convertToModel($movieDetails);
    }

    /**
     * @return list<Movie>
     */
    public function listAll(): array
    {
        return array_map($this->convertToModel(...), self::MOVIES);
    }
}
