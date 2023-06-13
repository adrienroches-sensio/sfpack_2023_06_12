<?php

namespace App\Controller;

use App\Form\MovieType;
use App\Model\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route(
        path: '/movies',
        name: 'app_movie_list',
        methods: ['GET']
    )]
    public function list(MovieRepository $movieRepository): Response
    {
        return $this->render('movie/list.html.twig', [
            'movies' => Movie::fromMovieEntities($movieRepository->listAll()),
        ]);
    }

    #[Route(
        path: '/movies/{movieSlug}',
        name: 'app_movie_details',
        requirements: [
            'movieSlug' => '\d{4}-\w+(-\w+)*',
        ],
        methods: ['GET']
    )]
    public function details(MovieRepository $movieRepository, string $movieSlug): Response
    {
        return $this->render('movie/details.html.twig', [
            'movie' => Movie::fromMovieEntity($movieRepository->getBySlug($movieSlug)),
        ]);
    }


    #[Route(
        path: '/movies/new',
        name: 'app_movie_new',
        methods: ['GET']
    )]
    public function new(): Response
    {
        $movieForm = $this->createForm(MovieType::class);

        return $this->render('movie/new.html.twig', [
            'movie_form' => $movieForm->createView(),
        ]);
    }
}
