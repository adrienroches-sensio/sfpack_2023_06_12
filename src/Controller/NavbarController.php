<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class NavbarController extends AbstractController
{
    public function main(string|null $currentMovieSlug = null): Response
    {
        $movieRepository = new MovieRepository();

        return $this->render('navbar.html.twig', [
            'movies' => $movieRepository->listAll(),
            'currentMovieSlug' => $currentMovieSlug,
        ]);
    }
}
