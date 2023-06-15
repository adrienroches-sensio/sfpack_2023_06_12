<?php

namespace App\Controller;

use App\Entity\Movie as MovieEntity;
use App\Entity\User;
use App\EventSubscriber\MovieAddedEvent;
use App\Form\MovieType;
use App\Model\Movie;
use App\Omdb\Api\OmdbApiClientInterface;
use App\Repository\MovieRepository;
use App\Security\Voter\MovieVoter;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class MovieController extends AbstractController
{
    public function __construct(
        private readonly MovieRepository $movieRepository,
        private readonly OmdbApiClientInterface $omdbApiClient,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Route(
        path: '/movies',
        name: 'app_movie_list',
        methods: ['GET']
    )]
    public function list(): Response
    {
        return $this->render('movie/list.html.twig', [
            'movies' => Movie::fromMovieEntities($this->movieRepository->listAll()),
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
    public function details(string $movieSlug): Response
    {
        $movie = Movie::fromMovieEntity($this->movieRepository->getBySlug($movieSlug));
        $this->denyAccessUnlessGranted(MovieVoter::MEETS_AGE_REQUIREMENTS, $movie);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route(
        path: '/movies/{imdbId}',
        name: 'app_movie_details_omdb',
        requirements: [
            'imdbId' => 'tt\d+',
        ],
        methods: ['GET']
    )]
    public function detailsFromOmdb(string $imdbId): Response
    {
        $movie = Movie::fromOmdb($this->omdbApiClient->getById($imdbId));
        $this->denyAccessUnlessGranted(MovieVoter::MEETS_AGE_REQUIREMENTS, $movie);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route(
        path: '/admin/movies/new',
        name: 'app_movie_new',
        methods: ['GET', 'POST']
    )]
    #[Route(
        path: '/admin/movies/{movieSlug}/edit',
        name: 'app_movie_edit',
        requirements: [
            'movieSlug' => MovieEntity::SLUG_FORMAT,
        ],
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
        string|null $movieSlug = null
    ): Response {
        $movie = new MovieEntity();
        if (null !== $movieSlug) {
            $movie = $this->movieRepository->getBySlug($movieSlug);
        }

        $movieForm = $this->createForm(MovieType::class, $movie);
        $movieForm->handleRequest($request);

        if ($movieForm->isSubmitted() && $movieForm->isValid()) {
            $this->movieRepository->save($movie, true);

            if (null === $movieSlug) {
                $this->eventDispatcher->dispatch(new MovieAddedEvent($this->getUser(), $movie));
            }

            return $this->redirectToRoute('app_movie_details', ['movieSlug' => $movie->getSlug()]);
        }

        return $this->render('movie/new.html.twig', [
            'movie_form' => $movieForm->createView(),
        ]);
    }
}
