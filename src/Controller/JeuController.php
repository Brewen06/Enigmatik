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

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/jeu')]
final class JeuController extends AbstractController
{
    #[Route(name: 'app_jeu_index', methods: ['GET'])]
    public function index(JeuRepository $jeuRepository, EnigmeRepository $enigmeRepository, Request $request, EquipeRepository $equipeRepository): Response
    {
        $equipeId = $request->query->get('equipe_id');
        $equipe = $equipeId ? $equipeRepository->find($equipeId) : null;
        $jeu = $jeuRepository->findOneBy([]);
        $canManageEnigmes = $this->isGranted('ROLE_PROF') || $this->isGranted('ROLE_ADMIN');
        $enigmes = $canManageEnigmes
            ? $enigmeRepository->findBy([], ['ordre' => 'ASC'])
            : $enigmeRepository->findBy(['active' => true], ['ordre' => 'ASC']);

        $timerSeconds = $this->extractTimerSeconds($jeu);

        return $this->render('jeu/index.html.twig', [
            'jeu' => $jeu,
            'enigmes' => $enigmes,
            'equipe' => $equipe,
            'timerSeconds' => $timerSeconds,
            'canManageEnigmes' => $canManageEnigmes,
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

    #[Route('/configuration', name: 'app_jeu_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PROF')]
    public function edit(Request $request, JeuRepository $jeuRepository, EntityManagerInterface $entityManager): Response
    {
        $jeu = $jeuRepository->findOneBy([]);

        if (!$jeu) {
            $jeu = new Jeu();
            $jeu->setTitre('Nouvelle partie');
            $entityManager->persist($jeu);
        }

        $form = $this->createForm(JeuType::class, $jeu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La configuration globale du jeu a été mise à jour !');

            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('jeu/edit.html.twig', [
            'jeu' => $jeu,
            'form' => $form->createView(),
        ]);
    }

    private function extractTimerSeconds(?Jeu $jeu): int
    {
        if (!$jeu) {
            return 0;
        }

        foreach ($jeu->getParametres() as $parametre) {
            $libelle = strtolower(trim((string) $parametre->getLibelle()));

            if (!in_array($libelle, ['durée du jeu (minutes)', 'duree du jeu (minutes)', 'duree_jeu_minutes'], true)) {
                continue;
            }

            $minutes = max(0, (int) $parametre->getValeur());

            return $minutes * 60;
        }

        return 0;
    }
}
