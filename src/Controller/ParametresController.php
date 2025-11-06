<?php

namespace App\Controller;

use App\Entity\Parametres;
use App\Form\ParametresType;
use App\Repository\ParametresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/parametres')]
final class ParametresController extends AbstractController
{
    #[Route(name: 'app_parametres_index', methods: ['GET'])]
    public function index(ParametresRepository $parametresRepository): Response
    {
        return $this->render('parametres/index.html.twig', [
            'parametres' => $parametresRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_parametres_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parametre = new Parametres();
        $form = $this->createForm(ParametresType::class, $parametre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($parametre);
            $entityManager->flush();

            return $this->redirectToRoute('app_parametres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/new.html.twig', [
            'parametre' => $parametre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parametres_show', methods: ['GET'])]
    public function show(Parametres $parametre): Response
    {
        return $this->render('parametres/show.html.twig', [
            'parametre' => $parametre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametres_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Parametres $parametre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParametresType::class, $parametre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_parametres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/edit.html.twig', [
            'parametre' => $parametre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parametres_delete', methods: ['POST'])]
    public function delete(Request $request, Parametres $parametre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parametre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($parametre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_parametres_index', [], Response::HTTP_SEE_OTHER);
    }
}
