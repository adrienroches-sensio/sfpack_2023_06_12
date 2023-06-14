<?php

declare(strict_types=1);

namespace App\Omdb\Bridge;

use App\Entity\Movie as MovieEntity;
use App\Omdb\Api\Movie;

interface OmdbToDatabaseImporterInterface
{
    public function importFromApiData(Movie $movie, bool $flush = false): MovieEntity;
}
