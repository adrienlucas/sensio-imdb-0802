<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route('/movie', name: 'movie')]
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $movie = [
            'title' => 'The Return of the King',
            'year' => 2003,
            'director' => 'Peter Jackson',
            'description' => 'A meek Hobbit from the Shire and eight companions set out on a journey to destroy the powerful One Ring and save Middle-earth from the Dark Lord Sauron.',
        ];

        return $this->render('movie/index.html.twig', [
            'movie' => $movie,
        ]);
    }
}
