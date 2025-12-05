<?php

namespace App\Controller;

use App\Entity\Parametre;
use App\Form\ParametreType;
use App\Repository\ParametreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/parametre')]
final class ParametreController extends AbstractController
{
    #[Route(name: 'app_parametre_index', methods: ['GET'])]
    public function index(ParametreRepository $parametreRepository): Response
    {
        return $this->render('parametre/index.html.twig', [
            'parametres' => $parametreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_parametre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parametre = new Parametre();
        $form = $this->createForm(ParametreType::class, $parametre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($parametre);
            $entityManager->flush();

            return $this->redirectToRoute('app_parametre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametre/new.html.twig', [
            'parametre' => $parametre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parametre_show', methods: ['GET'])]
    public function show(Parametre $parametre): Response
    {
        return $this->render('parametre/show.html.twig', [
            'parametre' => $parametre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Parametre $parametre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParametreType::class, $parametre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_parametre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametre/edit.html.twig', [
            'parametre' => $parametre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parametre_delete', methods: ['POST'])]
    public function delete(Request $request, Parametre $parametre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parametre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($parametre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_parametre_index', [], Response::HTTP_SEE_OTHER);
    }
}
