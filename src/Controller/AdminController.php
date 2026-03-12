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
    #[Route('/delete', name: 'app_admin_delete')]
        public function delete(): Response
        {
            return $this->render('admin/_delete_all.html.twig', [
                'controller_name' => 'AdminController',
            ]);
        }

}
