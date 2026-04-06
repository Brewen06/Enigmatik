<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use App\Repository\JeuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(JeuRepository $jeuRepository, Request $request, EquipeRepository $equipeRepository): Response
    {
        $equipeId = $request->getSession()->get('equipe_id');
        $equipe = $equipeId ? $equipeRepository->find($equipeId) : null;

        return $this->render('index.html.twig', [
            'jeux' => $jeuRepository->findAll(),
            'equipe' => $equipe,
        ]);
    }
}
