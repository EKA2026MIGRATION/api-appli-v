<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PersonRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductCancelledDateRepository extends EntityRepository
{
    /**
     * Returns all the ProductCancelledDate in an array
     */
    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->where('p.suppressed = 0')
            ->orderBy('p.date', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * Returns all the ProductCancelledDate by category and date
     */
    public function findAllByCategoryDate($categoryId, $date)
    {
        return $this->createQueryBuilder('p')
            ->where('p.date LIKE :date')
            ->andWhere('p.category = :categoryId')
            ->andWhere('p.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

        /**
     * Returns all the ProductCancelledDate by category and date
     */
    public function findCurrent()
    {
        return $this->createQueryBuilder('p')
            ->where('p.suppressed = 0')
            ->andWhere('p.date >= CURRENT_DATE()')
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the ProductCancelledDate by date
     */
    public function findAllByDate($date)
    {
        return $this->createQueryBuilder('p')
            ->where('p.date LIKE :date')
            ->andWhere('p.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the ProductCancelledDate by product and date
     */
    public function findAllByProductDate($productId, $date)
    {
        return $this->createQueryBuilder('p')
            ->where('p.date LIKE :date')
            ->andWhere('p.product = :productId')
            ->andWhere('p.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('productId', $productId . '%')
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the ProductCancelledDate if not suppressed
     */
    public function findOneById($productCancelledDateId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.productCancelledDateId = :productCancelledDateId')
            ->andWhere('p.suppressed = 0')
            ->setParameter('productCancelledDateId', $productCancelledDateId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
