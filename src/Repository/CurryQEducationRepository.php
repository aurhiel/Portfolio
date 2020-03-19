<?php

namespace App\Repository;

use App\Entity\CurryQEducation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CurryQEducation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurryQEducation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurryQEducation[]    findAll()
 * @method CurryQEducation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurryQEducationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CurryQEducation::class);
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
