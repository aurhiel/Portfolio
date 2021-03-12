<?php

namespace App\Repository;

use App\Entity\CurryQSkillCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CurryQSkillCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurryQSkillCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurryQSkillCategory[]    findAll()
 * @method CurryQSkillCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurryQSkillCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurryQSkillCategory::class);
    }

    // /**
    //  * @return CurryQSkillCategory[] Returns an array of CurryQSkillCategory objects
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
    public function findOneBySomeField($value): ?CurryQSkillCategory
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
