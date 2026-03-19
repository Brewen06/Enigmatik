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

    #[Route('/creer', name: 'app_enigme_creer', methods: ['GET', 'POST'])]
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
        $expectedSolutions = $this->extractNormalizedSolutions((string) ($enigme->getSolution() ?? ''));

        return $this->render('enigme/show.html.twig', [
            'enigme' => $enigme,
            'quizHasMultipleSolutions' => count($expectedSolutions) > 1,
        ]);
    }

    #[Route('/{id}/check', name: 'app_enigme_check', methods: ['POST'])]
    public function check(Request $request, Enigme $enigme): Response
    {
        $data = json_decode($request->getContent(), true);
        $answer = $data['answer'] ?? '';

        $expectedSolutions = $this->extractNormalizedSolutions((string) ($enigme->getSolution() ?? ''));
        $normalizedCodeSecret = $this->normalizeAnswer((string) ($enigme->getCodeSecret() ?? ''));
        $isValidAnswer = false;

        if ($expectedSolutions !== []) {
            if (is_array($answer)) {
                $normalizedAnswers = $this->normalizeAnswerArray($answer);
                $isValidAnswer = ($normalizedAnswers !== []) && ($normalizedAnswers === $expectedSolutions);
            } else {
                $normalizedAnswer = $this->normalizeAnswer((string) $answer);
                $isValidAnswer = count($expectedSolutions) === 1
                    && $normalizedAnswer !== ''
                    && $normalizedAnswer === $expectedSolutions[0];
            }
        } elseif (!is_array($answer)) {
            // Compatibilite pour les anciennes enigmes sans solution renseignee.
            $normalizedAnswer = $this->normalizeAnswer((string) $answer);
            $isValidAnswer = $normalizedAnswer !== '' && $normalizedCodeSecret !== '' && $normalizedAnswer === $normalizedCodeSecret;
        }

        if ($isValidAnswer) {
            return $this->json([
                'success' => true,
                'codeSecret' => $enigme->getCodeSecret(),
            ]);
        }

        return $this->json(['success' => false]);
    }

    private function normalizeAnswer(string $value): string
    {
        $normalized = trim($value);
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        return mb_strtolower($normalized);
    }

    /**
     * @return list<string>
     */
    private function extractNormalizedSolutions(string $solution): array
    {
        $parts = preg_split('/\R+/', $solution) ?: [];
        $normalized = array_map(fn($value) => $this->normalizeAnswer((string) $value), $parts);
        $normalized = array_values(array_unique(array_filter($normalized, fn(string $value) => $value !== '')));
        sort($normalized);

        return $normalized;
    }

    /**
     * @param array<mixed> $answers
     *
     * @return list<string>
     */
    private function normalizeAnswerArray(array $answers): array
    {
        $normalized = array_map(fn($value) => $this->normalizeAnswer((string) $value), $answers);
        $normalized = array_values(array_unique(array_filter($normalized, fn(string $value) => $value !== '')));
        sort($normalized);

        return $normalized;
    }

    #[Route('/{id}/modifier', name: 'app_enigme_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}/supprimer', name: 'app_enigme_delete', methods: ['POST'])]
    public function delete(Request $request, Enigme $enigme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $enigme->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($enigme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_enigme_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/supprimer-toutes', name: 'app_enigme_delete_all', methods: ['POST'])]
    public function deleteAll(Request $request, EnigmeRepository $enigmeRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_enigme_all', $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $enigmesASupprimer = $enigmeRepository->findAll();

        foreach ($enigmesASupprimer as $enigmeASupprimer) {
            $entityManager->remove($enigmeASupprimer);
        }

        $entityManager->flush();

        $deletedCount = count($enigmesASupprimer);

        if ($deletedCount > 0) {
            $this->addFlash('success', sprintf('%d énigme(s) supprimée(s).', $deletedCount));
        } else {
            $this->addFlash('success', 'Aucune énigme à supprimer.');
        }

        return $this->redirectToRoute('app_enigme_index');
    }
}
