<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class MovieAddedEvent extends Event
{
    public function __construct(
        public readonly User $user,
        public readonly Movie $movie,
    ) {
    }
}
