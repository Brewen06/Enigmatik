<?php

namespace App\Repository;

use App\Entity\Equipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipe>
 */
class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

    /**
     * @return list<Equipe>
     */
    public function findAllWithAvatar(): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.avatar', 'a')
            ->addSelect('a')
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<Equipe>
     */
    public function findForRanking(): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.avatar', 'a')
            ->addSelect('a')
            ->orderBy('e.position', 'DESC')
            ->addOrderBy('e.finishedAt', 'ASC')
            ->addOrderBy('e.startedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Equipe[] Returns an array of Equipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Equipe
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
