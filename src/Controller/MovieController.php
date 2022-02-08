<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route('/movie/{id}', name: 'movie')]
    public function index(Movie $movie): Response
    {
        return $this->render('movie/index.html.twig', [
            'movie' => $movie,
        ]);
    }
    
    #[Route('/list-movies', name: 'list_movie')]
    #[Route('/', name: 'homepage')]
    public function list(): Response
    {
        $entityManager = $this->container->get(EntityManagerInterface::class);
        $movies = $entityManager->getRepository(Movie::class)->findAll();

        return $this->render('movie/list.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/loaddemo', name: 'loaddemo')]
    public function loadDemo(): Response
    {
        /** @var Registry $doctrine */
//        $doctrine = $this->container->get('doctrine');
//        $entityManager = $doctrine->getManager();

        $entityManager = $this->container->get(EntityManagerInterface::class);

        // Create the first movie
        $movie = new Movie();
        $movie->setTitle('Don\'t look up');
        $movie->setDirector('Adam McKay');
        $movie->setYear(2021);
        $movie->setDescription('Two low-level astronomers must go on a giant media tour to warn mankind of an approaching comet that will destroy planet Earth.');

        $entityManager->persist($movie);

        // Create the second movie
        $movie = new Movie();
        $movie->setTitle('The Blues Brothers');
        $movie->setDirector('John Landis');
        $movie->setYear(1980);
        $movie->setDescription('Jake Blues rejoins with his brother Elwood after being released from prison, but the duo has just days to reunite their old R&B band and save the Catholic home where the two were raised, outrunning the police as they tear through Chicago.');

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
