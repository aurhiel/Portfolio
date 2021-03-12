<?php

namespace App\Repository;

use App\Entity\CurryQEducation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CurryQEducation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurryQEducation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurryQEducation[]    findAll()
 * @method CurryQEducation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurryQEducationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurryQEducation::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('e', 'e.id')
            // Order
            ->orderBy('e.year_start', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return CurryQEducation[] Returns an array of CurryQEducation objects
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
    public function findOneBySomeField($value): ?CurryQEducation
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
