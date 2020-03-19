<?php

namespace App\Repository;

use App\Entity\CurryQProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CurryQProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurryQProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurryQProject[]    findAll()
 * @method CurryQProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurryQProjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CurryQProject::class);
    }

    // /**
    //  * @return CurryQProject[] Returns an array of CurryQProject objects
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
    public function findOneBySomeField($value): ?CurryQProject
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
