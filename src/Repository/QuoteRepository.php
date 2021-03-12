<?php

namespace App\Repository;

use App\Entity\Quote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quote[]    findAll()
 * @method Quote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }

    public function findAll()
    {
        $qb = $this->createQueryBuilder('q')
            ->select('q AS quote')
            // Left joins
            ->leftJoin('q.client', 'c')
            ->addSelect('c')
            ->leftJoin('c.testimonial', 't')
            ->addSelect('t')
            // Retrieve invoices count
            ->leftJoin('q.invoices', 'invoices')
            ->addSelect('COUNT(invoices) AS invoices_count, SUM(invoices.amount) AS invoices_total_amount')
            // Group By
            ->groupBy('q.id')
            // Order
            ->orderBy('q.date_signed', 'ASC');

        $results  = $qb->getQuery()->getResult();
        $quotes   = array();

        foreach ($results as $result) {
            $quote = $result['quote'];

            // Set invoices count & total amount into quote entity
            $quote->setInvoicesCount($result['invoices_count']);
            $quote->setInvoicesTotalAmount($result['invoices_total_amount']);

            $quotes[] = $quote;
        }

        return $quotes;
    }

    public function findTotalAmountsGroupByYear()
    {
        return $this->createQueryBuilder('q')
            ->select('SUM(q.amount) total_amount, YEAR(q.date_signed) AS year_signed')
            // Group By
            ->groupBy('year_signed')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Quote[] Returns an array of Quote objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Quote
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
