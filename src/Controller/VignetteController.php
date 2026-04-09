<?php

namespace App\Controller;

use App\Entity\Vignette;
use App\Form\VignetteType;
use App\Repository\VignetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vignette')]
#[IsGranted('ROLE_PROF')]
final class VignetteController extends AbstractController
{
    #[Route(name: 'app_vignette_index', methods: ['GET'])]
    public function index(VignetteRepository $vignetteRepository): Response
    {
        return $this->render('vignette/index.html.twig', [
            'vignettes' => $vignetteRepository->findAll(),
        ]);
    }

    #[Route('/creer', name: 'app_vignette_new', methods: ['GET', 'POST'])]
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

    #[Route('/{id}/modifier', name: 'app_vignette_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}/supprimer', name: 'app_vignette_delete', methods: ['POST'])]
    public function delete(Request $request, Vignette $vignette, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $vignette->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vignette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vignette_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/quick-upload', name: 'app_vignette_quick_upload', methods: ['POST'])]
    public function quickUpload(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isCsrfTokenValid('vignette_quick_upload', $request->request->getString('_token'))) {
            return $this->json(['success' => false, 'message' => 'Jeton CSRF invalide.'], Response::HTTP_FORBIDDEN);
        }

        $file = $request->files->get('file');
        if (!$file instanceof UploadedFile) {
            return $this->json(['success' => false, 'message' => 'Aucun fichier reçu.'], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array((string) $file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return $this->json(['success' => false, 'message' => 'Format d image non supporté.'], Response::HTTP_BAD_REQUEST);
        }

        $baseName = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = preg_replace('/[^a-z0-9]+/i', '-', strtolower($baseName)) ?: 'vignette';
        $extension = strtolower((string) ($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'jpg'));
        $fileName = sprintf('%s-%s.%s', $safeBaseName, uniqid('', true), $extension);

        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/images/Vignettes';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0775, true);
        }

        $file->move($uploadDirectory, $fileName);

        $vignette = new Vignette();
        $vignette->setImage('public/images/Vignettes/' . $fileName);
        $vignette->setInformation($baseName !== '' ? $baseName : 'Vignette importée');

        $entityManager->persist($vignette);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'vignette' => [
                'id' => $vignette->getId(),
                'information' => $vignette->getInformation(),
                'image' => $vignette->getImage(),
            ],
        ]);
    }

    #[Route('/quick-delete', name: 'app_vignette_quick_delete', methods: ['POST'])]
    public function quickDelete(Request $request, VignetteRepository $vignetteRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isCsrfTokenValid('vignette_quick_delete', $request->request->getString('_token'))) {
            return $this->json(['success' => false, 'message' => 'Jeton CSRF invalide.'], Response::HTTP_FORBIDDEN);
        }

        $vignetteId = $request->request->getInt('id');
        $vignette = $vignetteRepository->find($vignetteId);

        if (!$vignette instanceof Vignette) {
            return $this->json(['success' => false, 'message' => 'Vignette introuvable.'], Response::HTTP_NOT_FOUND);
        }

        foreach (iterator_to_array($vignette->getEnigmes()) as $enigme) {
            $enigme->setVignette(null);
        }

        $imagePath = $vignette->getImage();
        if (is_string($imagePath)) {
            $absolutePath = $this->getParameter('kernel.project_dir') . '/' . $imagePath;
            if (is_file($absolutePath)) {
                @unlink($absolutePath);
            }
        }

        $entityManager->remove($vignette);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }
}
