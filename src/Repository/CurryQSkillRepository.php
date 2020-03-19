<?php

namespace App\Repository;

use App\Entity\CurryQSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CurryQSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurryQSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurryQSkill[]    findAll()
 * @method CurryQSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurryQSkillRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CurryQSkill::class);
    }

    // /**
    //  * @return CurryQSkill[] Returns an array of CurryQSkill objects
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
    public function findOneBySomeField($value): ?CurryQSkill
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
