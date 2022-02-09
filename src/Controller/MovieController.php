<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
//    #[Route('/movie/{id_movie}', name: 'movie')]
//    #[ParamConverter('id_movie', Movie::class, options: ['mapping' => ['id_movie' => 'id']])]
    #[Route('/movie/{id}', name: 'movie')]
    public function index(Movie $movie): Response
    {
        return $this->render('movie/index.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/list-movies/{name}', name: 'list_movie', defaults: ['name' => null])]
    public function list(?Genre $genre = null): Response
    {
        $entityManager = $this->container->get(EntityManagerInterface::class);

        if($genre === null) {
            $movies = $entityManager->getRepository(Movie::class)->findAll();
        } else {
            $movies = $entityManager->getRepository(Movie::class)->findByGenre($genre);
        }

        return $this->render('movie/list.html.twig', [
            'movies' => $movies,
            'genre' => $genre,
        ]);
    }

    #[Route('/loaddemo', name: 'loaddemo')]
    public function loadDemo(): Response
    {
        /** @var Registry $doctrine */
//        $doctrine = $this->container->get('doctrine');
//        $entityManager = $doctrine->getManager();

        $entityManager = $this->container->get(EntityManagerInterface::class);

        // Create genres
        $musical = new Genre();
        $musical->setName('musical');

        $entityManager->persist($musical);

        $comedy = new Genre();
        $comedy->setName('comedy');

        $entityManager->persist($comedy);

        // Create the first movie
        $movie = new Movie();
        $movie->setTitle('Don\'t look up');
        $movie->setDirector('Adam McKay');
        $movie->setYear(2021);
        $movie->setDescription('Two low-level astronomers must go on a giant media tour to warn mankind of an approaching comet that will destroy planet Earth.');

        $comedy->addMovie($movie);
        $entityManager->persist($movie);

        // Create the second movie
        $movie = new Movie();
        $movie->setTitle('The Blues Brothers');
        $movie->setDirector('John Landis');
        $movie->setYear(1980);
        $movie->setDescription('Jake Blues rejoins with his brother Elwood after being released from prison, but the duo has just days to reunite their old R&B band and save the Catholic home where the two were raised, outrunning the police as they tear through Chicago.');

        $musical->addMovie($movie);
        $comedy->addMovie($movie);
        $entityManager->persist($movie);

        // Create a movie without Genre
        $movie = new Movie();
        $movie->setTitle('A Clockwork Orange');
        $movie->setDirector('Stanley Kubrick');
        $movie->setYear(1971);
        $movie->setDescription('In the future, a sadistic gang leader is imprisoned and volunteers for a conduct-aversion experiment, but it doesn\'t go as planned.');

        $entityManager->persist($movie);

        $entityManager->flush();

        return new Response('OK');
    }

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services[] = EntityManagerInterface::class;
//        $services['doctrine'] = Registry::class;

        return $services;
    }
}
