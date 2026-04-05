<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EnigmeRepository;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/equipe')]
final class EquipeController extends AbstractController
{
    #[Route(name: 'app_equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository, EnigmeRepository $enigmeRepository): Response
    {
        $enigmes = $enigmeRepository->findBy(['active' => true], ['ordre' => 'ASC']);
        $rows = $this->buildTeamRows($equipeRepository->findAllWithAvatar(), $enigmes);

        return $this->render('equipe/index.html.twig', [
            'equipes' => $rows,
            'totalEnigmes' => count($enigmes),
        ]);
    }

    #[Route('/classement', name: 'app_equipe_classement', methods: ['GET'])]
    #[IsGranted('ROLE_PROF')]
    public function classement(EquipeRepository $equipeRepository, EnigmeRepository $enigmeRepository): Response
    {
        $enigmes = $enigmeRepository->findBy(['active' => true], ['ordre' => 'ASC']);
        $rows = $this->buildTeamRows($equipeRepository->findForRanking(), $enigmes);

        usort($rows, static function (array $left, array $right): int {
            $solvedSort = $right['solvedCount'] <=> $left['solvedCount'];

            if ($solvedSort !== 0) {
                return $solvedSort;
            }

            if ($left['isFinished'] && !$right['isFinished']) {
                return -1;
            }

            if (!$left['isFinished'] && $right['isFinished']) {
                return 1;
            }

            if ($left['durationSeconds'] !== null && $right['durationSeconds'] !== null) {
                $durationSort = $left['durationSeconds'] <=> $right['durationSeconds'];

                if ($durationSort !== 0) {
                    return $durationSort;
                }
            }

            $currentEnigmeSort = $right['enigmeActuelle'] <=> $left['enigmeActuelle'];

            if ($currentEnigmeSort !== 0) {
                return $currentEnigmeSort;
            }

            return $left['id'] <=> $right['id'];
        });

        foreach ($rows as $index => &$row) {
            $row['rank'] = $index + 1;
        }
        unset($row);

        return $this->render('equipe/classement.html.twig', [
            'equipes' => $rows,
            'totalEnigmes' => count($enigmes),
        ]);
    }

    #[Route('/creer', name: 'app_equipe_creer', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $equipe->setPosition(0);
            $equipe->setEnigmeActuelle(1);
            $equipe->setStartedAt(new \DateTime());
            $entityManager->persist($equipe);
            $entityManager->flush();

            // Enregistrer l'ID de l'équipe en session
            $request->getSession()->set('equipe_id', $equipe->getId());

            return $this->redirectToRoute('app_jeu_index', ['equipe_id' => $equipe->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $equipe->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param list<Equipe> $equipes
     * @param list<\App\Entity\Enigme> $enigmes
     *
     * @return list<array<string, mixed>>
     */
    private function buildTeamRows(array $equipes, array $enigmes): array
    {
        $enigmeIds = array_values(array_filter(
            array_map(static fn($enigme): int => (int) ($enigme->getId() ?? 0), $enigmes),
            static fn(int $id): bool => $id > 0
        ));

        $rows = [];

        foreach ($equipes as $equipe) {
            $resolvedIds = array_values(array_intersect($equipe->getEnigmesResolues(), $enigmeIds));
            sort($resolvedIds);

            $enigmesProgression = [];
            foreach ($enigmes as $enigme) {
                $enigmeId = (int) ($enigme->getId() ?? 0);
                $enigmesProgression[] = [
                    'id' => $enigmeId,
                    'ordre' => (int) ($enigme->getOrdre() ?? 0),
                    'titre' => (string) ($enigme->getTitre() ?? ''),
                    'resolved' => in_array($enigmeId, $resolvedIds, true),
                ];
            }

            $startedAt = $equipe->getStartedAt();
            $finishedAt = $equipe->getFinishedAt();
            $durationSeconds = null;

            if ($startedAt !== null) {
                $endTime = $finishedAt ?? new \DateTimeImmutable();
                $durationSeconds = max(0, $endTime->getTimestamp() - $startedAt->getTimestamp());
            }

            $solvedCount = count($resolvedIds);
            $totalEnigmes = count($enigmeIds);
            $currentEnigme = (int) ($equipe->getEnigmeActuelle() ?? 1);
            $isAllSolved = $totalEnigmes > 0 && $solvedCount >= $totalEnigmes;
            $displayEnigme = $totalEnigmes > 0 ? min(max(1, $currentEnigme), $totalEnigmes) : null;

            $rows[] = [
                'id' => (int) ($equipe->getId() ?? 0),
                'nom' => (string) ($equipe->getNom() ?? ''),
                'avatarImage' => $equipe->getAvatar()?->getImage(),
                'avatarNom' => $equipe->getAvatar()?->getNom(),
                'position' => (int) ($equipe->getPosition() ?? 0),
                'enigmeActuelle' => $currentEnigme,
                'enigmeActuelleDisplay' => $displayEnigme,
                'startedAt' => $startedAt,
                'finishedAt' => $finishedAt,
                'isFinished' => $finishedAt !== null,
                'isAllSolved' => $isAllSolved,
                'durationSeconds' => $durationSeconds,
                'solvedCount' => $solvedCount,
                'progressPercent' => $totalEnigmes > 0 ? (int) round(($solvedCount / $totalEnigmes) * 100) : 0,
                'resolvedIds' => $resolvedIds,
                'enigmesProgression' => $enigmesProgression,
            ];
        }

        return $rows;
    }
}
