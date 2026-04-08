<?php

namespace App\Controller;

use App\Entity\Jeu;
use App\Form\TimerSettingsType;
use App\Repository\JeuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/parametre')]
final class ParametreController extends AbstractController
{
    #[Route(name: 'app_parametre_index', methods: ['GET', 'POST'])]
    public function index(Request $request, JeuRepository $jeuRepository, EntityManagerInterface $entityManager): Response
    {
        $jeu = $jeuRepository->findOneBy([]);

        if (!$jeu) {
            $jeu = new Jeu();
            $jeu->setTitre('Nouvelle partie');
            $entityManager->persist($jeu);
        }

        $timerForm = $this->createForm(TimerSettingsType::class, $jeu);
        $timerForm->handleRequest($request);

        if ($timerForm->isSubmitted() && $timerForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le chronomètre a été mis à jour.');

            return $this->redirectToRoute('app_parametre_index');
        }

        $timerMinutes = $jeu?->getTimerMinutes() ?? 0;

        return $this->render('parametre/index.html.twig', [
            'timerMinutes' => $timerMinutes,
            'timerForm' => $timerForm->createView(),
        ]);
    }
}
