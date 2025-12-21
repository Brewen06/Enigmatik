<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            ->where('e.finishedAt IS NULL')
            ->orderBy('e.startedAt', 'DESC')
            ->getQuery()
            ->getResult();

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
}
