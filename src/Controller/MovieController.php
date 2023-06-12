<?php

namespace App\Controller;

use App\Model\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route(
        path: '/movies/{movieSlug}',
        name: 'app_movie_details',
        requirements: [
            'movieSlug' => '\d{4}-\w+(-\w+)*',
        ],
        methods: ['GET']
    )]
    public function details(string $movieSlug): Response
    {
        $movieRepository = new MovieRepository();

        return $this->render('movie/details.html.twig', [
            'movie' => $movieRepository->getBySlug($movieSlug),
        ]);
    }
}
