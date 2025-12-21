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
        $jeu = $jeuRepository->findOneBy([]);

        return $this->render('jeu/index.html.twig', [
            'jeu' => $jeu,
            'enigmes' => $enigmeRepository->findAll(),
            'equipe' => $equipe,
        ]);
    }

    #[Route('/validate-final-code', name: 'app_jeu_validate_final_code', methods: ['POST'])]
    public function validateFinalCode(Request $request, JeuRepository $jeuRepository): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $submittedCode = $data['code'] ?? '';
        
        $jeu = $jeuRepository->findOneBy([]);
        $correctCode = $jeu ? $jeu->getCodeFinal() : '';

        if ($correctCode && strtoupper(trim($submittedCode)) === strtoupper($correctCode)) {
            return $this->json([
                'success' => true,
                'message' => 'Félicitations ! Vous avez déverrouillé le système !'
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Code incorrect. Vérifiez vos indices.'
        ]);
    }
}