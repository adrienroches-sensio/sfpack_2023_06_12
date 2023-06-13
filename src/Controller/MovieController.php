<?php

namespace App\Controller;

use App\Entity\Movie as MovieEntity;
use App\Form\MovieType;
use App\Model\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
            'movieSlug' => MovieEntity::SLUG_FORMAT,
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
        methods: ['GET', 'POST']
    )]
    #[Route(
        path: '/movies/{movieSlug}/edit',
        name: 'app_movie_edit',
        requirements: [
            'movieSlug' => MovieEntity::SLUG_FORMAT,
        ],
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
        MovieRepository $movieRepository,
        string|null $movieSlug = null
    ): Response {
        $movie = new MovieEntity();
        if (null !== $movieSlug) {
            $movie = $movieRepository->getBySlug($movieSlug);
        }

        $movieForm = $this->createForm(MovieType::class, $movie);
        $movieForm->handleRequest($request);

        if ($movieForm->isSubmitted() && $movieForm->isValid()) {
            $movieRepository->save($movie, true);

            return $this->redirectToRoute('app_movie_details', ['movieSlug' => $movie->getSlug()]);
        }

        return $this->render('movie/new.html.twig', [
            'movie_form' => $movieForm->createView(),
        ]);
    }
}
