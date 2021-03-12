<?php

namespace App\Repository;

use App\Entity\CurryQCareer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CurryQCareer|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurryQCareer|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurryQCareer[]    findAll()
 * @method CurryQCareer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurryQCareerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurryQCareer::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('c', 'c.id')
            // Order
            ->orderBy('c.date_start', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return CurryQCareer[] Returns an array of CurryQCareer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CurryQCareer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
