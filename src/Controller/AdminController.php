<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_PROF')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(EquipeRepository $equipeRepository): Response
    {
        $activeGames = $equipeRepository->createQueryBuilder('e')
            ->select('e.id, e.nom, e.startedAt, e.enigmeActuelle, a.image AS avatarImage')
            ->leftJoin('e.avatar', 'a')
            ->where('e.finishedAt IS NULL')
            ->orderBy('e.startedAt', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $historyGames = $equipeRepository->createQueryBuilder('e')
            ->where('e.finishedAt IS NOT NULL')
            ->orderBy('e.finishedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/dashboard.html.twig', [
            'activeGames' => $activeGames,
            'historyGames' => $historyGames,
        ]);
    }

    #[Route('/delete', name: 'app_admin_delete', methods: ['GET', 'POST'])]
    public function deleteAll(Request $request, EntityManagerInterface $entityManager, EquipeRepository $equipeRepository): Response
    {
        if (!$request->isMethod('POST')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        if (!$this->isCsrfTokenValid('delete_finished_games', $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $finishedGames = $equipeRepository->createQueryBuilder('e')
            ->where('e.finishedAt IS NOT NULL')
            ->getQuery()
            ->getResult();

        foreach ($finishedGames as $finishedGame) {
            foreach ($finishedGame->getAvatars() as $avatar) {
                $avatar->setEquipe(null);
            }

            $entityManager->remove($finishedGame);
        }

        $entityManager->flush();

        $deletedCount = count($finishedGames);

        if ($deletedCount > 0) {
            $this->addFlash('success', sprintf('%d partie(s) terminée(s) supprimée(s).', $deletedCount));
        } else {
            $this->addFlash('success', 'Aucune partie terminée à supprimer.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

}
