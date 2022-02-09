<?php

namespace App\Controller;

use App\Form\MovieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin')]
    public function index(Request $request): Response
    {
        $movieForm = $this->createForm(MovieType::class);
        $movieForm->add('submit', SubmitType::class);

        $movieForm->handleRequest($request);

        if($movieForm->isSubmitted() && $movieForm->isValid()) {
            $this->entityManager->persist($movieForm->getData());
            $this->entityManager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('admin/index.html.twig', [
            'movie_form' => $movieForm->createView()
        ]);
    }
}
