<?php

namespace App\Controller;

use App\Entity\Jeu;
use App\Form\JeuType;
use App\Repository\EnigmeRepository;
use App\Repository\EquipeRepository;
use App\Repository\JeuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/jeu')]
final class JeuController extends AbstractController
{
    #[Route(name: 'app_jeu_index', methods: ['GET'])]
    public function index(JeuRepository $jeuRepository, EnigmeRepository $enigmeRepository, Request $request, EquipeRepository $equipeRepository): Response
    {
        $equipeId = $request->query->get('equipe_id');
        $equipe = $equipeId ? $equipeRepository->find($equipeId) : null;

        return $this->render('jeu/index.html.twig', [
            'jeux' => $jeuRepository->findAll(),
            'enigmes' => $enigmeRepository->findAll(),
            'equipe' => $equipe,
        ]);
    }
}