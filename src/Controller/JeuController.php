<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Jeu;
use App\Form\JeuType;
use App\Repository\EnigmeRepository;
use App\Repository\EquipeRepository;
use App\Repository\JeuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $equipeId = (int) $request->query->get('equipe_id', 0);

        if ($equipeId <= 0) {
            $equipeId = (int) $request->getSession()->get('equipe_id', 0);
        }

        $equipe = $equipeId > 0 ? $equipeRepository->find($equipeId) : null;

        if ($equipe) {
            $request->getSession()->set('equipe_id', $equipe->getId());
        }

        $jeu = $jeuRepository->findOneBy([]);
        $canManageEnigmes = $this->isGranted('ROLE_PROF') || $this->isGranted('ROLE_ADMIN');
        $enigmes = $canManageEnigmes
            ? $enigmeRepository->findBy([], ['ordre' => 'ASC'])
            : $enigmeRepository->findBy(['active' => true], ['ordre' => 'ASC']);
        $resolvedEnigmeIds = $equipe?->getEnigmesResolues() ?? [];
        $unlockedEnigmeCount = $canManageEnigmes
            ? count($enigmes)
            : $this->computeUnlockedEnigmeCount($equipe, $enigmes, $resolvedEnigmeIds);
        $recoveredHints = $this->buildRecoveredHints($equipe, $enigmeRepository);

        $timerSeconds = $this->extractTimerSeconds($jeu);
        $timerRuntime = $this->computeTimerRuntime($equipe, $timerSeconds);

        return $this->render('jeu/index.html.twig', [
            'jeu' => $jeu,
            'enigmes' => $enigmes,
            'equipe' => $equipe,
            'timerSeconds' => $timerRuntime['remainingSeconds'],
            'timerLocked' => $timerRuntime['locked'],
            'canManageEnigmes' => $canManageEnigmes,
            'recoveredHints' => $recoveredHints,
            'resolvedEnigmeIds' => $resolvedEnigmeIds,
            'unlockedEnigmeCount' => $unlockedEnigmeCount,
        ]);
    }

    #[Route('/validate-final-code', name: 'app_jeu_validate_final_code', methods: ['POST'])]
    public function validateFinalCode(Request $request, JeuRepository $jeuRepository, EquipeRepository $equipeRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $submittedCode = $data['code'] ?? '';
        $submittedTeamId = (int) ($data['teamId'] ?? 0);

        $equipeId = $submittedTeamId > 0
            ? $submittedTeamId
            : (int) $request->getSession()->get('equipe_id', 0);

        $jeu = $jeuRepository->findOneBy([]);
        $correctCode = $jeu ? $jeu->getCodeFinal() : '';

        if ($correctCode && strtoupper(trim($submittedCode)) === strtoupper($correctCode)) {
            if ($equipeId > 0) {
                $equipe = $equipeRepository->find($equipeId);

                if ($equipe && $equipe->getFinishedAt() === null) {
                    $equipe->setFinishedAt(new \DateTimeImmutable());
                    $entityManager->flush();
                }
            }

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
    #[Route('/victoire', name: 'app_jeu_victoire', methods: ['GET'])]
    public function victoire(): Response
    {
        return $this->render('jeu/victoire.html.twig');
    }

    /**
     * @return list<array{ordre: int, titre: string, indice: string}>
     */
    private function buildRecoveredHints(?Equipe $equipe, EnigmeRepository $enigmeRepository): array
    {
        if (!$equipe) {
            return [];
        }

        $resolvedIds = $equipe->getEnigmesResolues();

        if ($resolvedIds === []) {
            return [];
        }

        $resolvedEnigmes = $enigmeRepository->findBy(['id' => $resolvedIds], ['ordre' => 'ASC']);
        $hints = [];

        foreach ($resolvedEnigmes as $resolvedEnigme) {
            $indice = trim((string) ($resolvedEnigme->getIndice() ?? ''));

            if ($indice === '') {
                continue;
            }

            $hints[] = [
                'ordre' => (int) ($resolvedEnigme->getOrdre() ?? 0),
                'titre' => (string) ($resolvedEnigme->getTitre() ?? ''),
                'indice' => $indice,
            ];
        }

        return $hints;
    }

    /**
     * @param list<\App\Entity\Enigme> $enigmes
     * @param list<int> $resolvedEnigmeIds
     */
    private function computeUnlockedEnigmeCount(?Equipe $equipe, array $enigmes, array $resolvedEnigmeIds): int
    {
        $totalEnigmes = count($enigmes);

        if ($totalEnigmes === 0) {
            return 0;
        }

        if (!$equipe) {
            return 1;
        }

        $resolvedSet = array_fill_keys($resolvedEnigmeIds, true);
        $resolvedActiveCount = 0;

        foreach ($enigmes as $enigme) {
            $enigmeId = (int) ($enigme->getId() ?? 0);

            if ($enigmeId > 0 && isset($resolvedSet[$enigmeId])) {
                $resolvedActiveCount++;
            }
        }

        return min($totalEnigmes, $resolvedActiveCount + 1);
    }

    private function extractTimerSeconds(?Jeu $jeu): int
    {
        if (!$jeu) {
            return 0;
        }

        $configuredTimerMinutes = $jeu->getTimerMinutes();
        if ($configuredTimerMinutes > 0) {
            return $configuredTimerMinutes * 60;
        }

        foreach ($jeu->getParametres() as $parametre) {
            if (!method_exists($parametre, 'getLibelle') || !method_exists($parametre, 'getValeur')) {
                continue;
            }

            $getLibelle = 'getLibelle';
            $getValeur = 'getValeur';
            $libelle = strtolower(trim((string) $parametre->{$getLibelle}()));

            if (!in_array($libelle, ['durée du jeu (minutes)', 'duree du jeu (minutes)', 'duree_jeu_minutes'], true)) {
                continue;
            }

            $minutes = max(0, (int) $parametre->{$getValeur}());

            return $minutes * 60;
        }

        return 0;
    }

    /**
     * @return array{remainingSeconds: int, locked: bool}
     */
    private function computeTimerRuntime(?Equipe $equipe, int $timerSeconds): array
    {
        if ($timerSeconds <= 0 || !$equipe || !$equipe->getStartedAt()) {
            return [
                'remainingSeconds' => max(0, $timerSeconds),
                'locked' => false,
            ];
        }

        $startedAt = $equipe->getStartedAt();
        $endTime = $equipe->getFinishedAt() ?? new \DateTimeImmutable();
        $elapsedSeconds = max(0, $endTime->getTimestamp() - $startedAt->getTimestamp());
        $remainingSeconds = max(0, $timerSeconds - $elapsedSeconds);

        return [
            'remainingSeconds' => $remainingSeconds,
            'locked' => $equipe->getFinishedAt() !== null,
        ];
    }
}
