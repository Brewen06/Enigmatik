<?php

namespace App\Controller;

use App\Entity\Enigme;
use App\Form\EnigmeType;
use App\Repository\EnigmeRepository;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
    public function check(
        Request $request,
        Enigme $enigme,
        EntityManagerInterface $entityManager,
        EquipeRepository $equipeRepository,
        EnigmeRepository $enigmeRepository
    ): Response {
        $data = json_decode($request->getContent(), true);
        $answer = $data['answer'] ?? '';

        $expectedSolutions = $this->extractNormalizedSolutions((string) ($enigme->getSolution() ?? ''));
        $indice = $this->getIndiceOrNull($enigme);
        $normalizedIndice = $this->normalizeAnswer((string) ($indice ?? ''));
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
            $normalizedAnswer = $this->normalizeAnswer((string) $answer);
            $isValidAnswer = $normalizedAnswer !== '' && $normalizedIndice !== '' && $normalizedAnswer === $normalizedIndice;
        }

        if ($isValidAnswer) {
            $this->updateEquipeProgression($request, $enigme, $entityManager, $equipeRepository, $enigmeRepository);

            return $this->json([
                'success' => true,
                'indice' => $indice,
            ]);
        }

        return $this->json(['success' => false]);
    }

    private function getIndiceOrNull(Enigme $enigme): ?string
    {
        $getter = 'getIndice';

        if (!is_callable([$enigme, $getter])) {
            return null;
        }

        $value = $enigme->{$getter}();

        return $value !== null ? (string) $value : null;
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

    private function updateEquipeProgression(
        Request $request,
        Enigme $enigme,
        EntityManagerInterface $entityManager,
        EquipeRepository $equipeRepository,
        EnigmeRepository $enigmeRepository
    ): void {
        $session = $request->getSession();

        if (!$session) {
            return;
        }

        $equipeId = (int) $session->get('equipe_id', 0);

        if ($equipeId <= 0) {
            return;
        }

        $equipe = $equipeRepository->find($equipeId);

        if (!$equipe) {
            return;
        }

        $isNewResolution = $equipe->addEnigmeResolue((int) $enigme->getId());

        if (!$isNewResolution) {
            return;
        }

        $solvedCount = $equipe->getNombreEnigmesResolues();
        $equipe->setPosition($solvedCount);

        $enigmeOrdre = max(1, (int) ($enigme->getOrdre() ?? 1));
        $equipe->setEnigmeActuelle(max((int) ($equipe->getEnigmeActuelle() ?? 1), $enigmeOrdre + 1));

        $activeEnigmes = $enigmeRepository->findBy(['active' => true]);
        $totalActiveEnigmes = count($activeEnigmes);

        if ($totalActiveEnigmes > 0 && $solvedCount >= $totalActiveEnigmes && $equipe->getFinishedAt() === null) {
            $equipe->setFinishedAt(new \DateTimeImmutable());
        }

        $entityManager->flush();
    }

    private function toggleAfficherFrise(Enigme $enigme): void
    {
        $methodPairs = [
            ['isAfficherFrise', 'setAfficherFrise'],
            ['getAfficherFrise', 'setAfficherFrise'],
            ['isFrise', 'setFrise'],
            ['getFrise', 'setFrise'],
        ];

        foreach ($methodPairs as [$getter, $setter]) {
            if (is_callable([$enigme, $getter]) && is_callable([$enigme, $setter])) {
                $currentValue = (bool) $enigme->{$getter}();
                $enigme->{$setter}(!$currentValue);

                return;
            }
        }

        throw new \LogicException('Aucun couple getter/setter de frise compatible trouvé sur Enigme.');
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

    #[Route('/{id}/toggle-active', name: 'app_enigme_toggle_active', methods: ['POST'])]
    #[IsGranted('ROLE_PROF')]
    public function toggleActive(Request $request, Enigme $enigme, EntityManagerInterface $entityManager): Response
    {
        $tokenId = 'toggle_active' . $enigme->getId();

        if (!$this->isCsrfTokenValid($tokenId, $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $enigme->setActive(!$enigme->isActive());
        $entityManager->flush();

        $this->addFlash(
            'success',
            sprintf('Énigme "%s" %s.', (string) $enigme->getTitre(), $enigme->isActive() ? 'activée' : 'désactivée')
        );

        $referer = $request->headers->get('referer');

        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_enigme_index');
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
    #[Route('/{id}/frise', name: 'app_enigme_frise', methods: ['GET'])]
    public function frise(Enigme $enigme): Response
    {
        return $this->render('enigme/frise.html.twig', [
            'enigme' => $enigme,
        ]);
    }

    #[Route('/{id}/frise/toggle', name: 'app_enigme_frise_toggle', methods: ['POST'])]
    public function toggleFrise(Request $request, Enigme $enigme, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('frise' . $enigme->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->toggleAfficherFrise($enigme);
        $entityManager->flush();

        $referer = $request->headers->get('referer');

        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_enigme_frise', ['id' => $enigme->getId()]);
    }
}
