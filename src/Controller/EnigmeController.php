<?php

namespace App\Controller;

use App\Entity\Enigme;
use App\Form\EnigmeType;
use App\Repository\EnigmeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/enigme')]
final class EnigmeController extends AbstractController
{
    #[Route(name: 'app_enigme_index', methods: ['GET'])]
    public function index(EnigmeRepository $enigmeRepository): Response
    {
        return $this->render('enigme/index.html.twig', [
            'enigmes' => $enigmeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_enigme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $enigme = new Enigme();
        $form = $this->createForm(EnigmeType::class, $enigme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($enigme);
            $entityManager->flush();

            return $this->redirectToRoute('app_enigme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enigme/new.html.twig', [
            'enigme' => $enigme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_enigme_show', methods: ['GET'])]
    public function show(Enigme $enigme): Response
    {
        return $this->render('enigme/show.html.twig', [
            'enigme' => $enigme,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_enigme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enigme $enigme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EnigmeType::class, $enigme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_enigme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enigme/edit.html.twig', [
            'enigme' => $enigme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_enigme_delete', methods: ['POST'])]
    public function delete(Request $request, Enigme $enigme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enigme->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($enigme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_enigme_index', [], Response::HTTP_SEE_OTHER);
    }
}
