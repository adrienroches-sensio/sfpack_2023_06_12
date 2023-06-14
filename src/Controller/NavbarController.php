<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class NavbarController extends AbstractController
{
    public function __construct(
        private readonly MovieRepository $movieRepository,
    ) {
    }

    public function main(string|null $currentMovieSlug = null): Response
    {
        return $this->render('navbar.html.twig', [
            'movies' => Movie::fromMovieEntities($this->movieRepository->listAll()),
            'currentMovieSlug' => $currentMovieSlug,
        ]);
    }
}
