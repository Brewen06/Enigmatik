<?php

namespace App\Controller;

use App\Entity\Vignette;
use App\Form\VignetteType;
use App\Repository\VignetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/vignette')]
final class VignetteController extends AbstractController
{
    #[Route(name: 'app_vignette_index', methods: ['GET'])]
    public function index(VignetteRepository $vignetteRepository): Response
    {
        return $this->render('vignette/index.html.twig', [
            'vignettes' => $vignetteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vignette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vignette = new Vignette();
        $form = $this->createForm(VignetteType::class, $vignette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vignette);
            $entityManager->flush();

            return $this->redirectToRoute('app_vignette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vignette/new.html.twig', [
            'vignette' => $vignette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vignette_show', methods: ['GET'])]
    public function show(Vignette $vignette): Response
    {
        return $this->render('vignette/show.html.twig', [
            'vignette' => $vignette,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vignette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vignette $vignette, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VignetteType::class, $vignette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vignette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vignette/edit.html.twig', [
            'vignette' => $vignette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vignette_delete', methods: ['POST'])]
    public function delete(Request $request, Vignette $vignette, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vignette->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vignette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vignette_index', [], Response::HTTP_SEE_OTHER);
    }
}
