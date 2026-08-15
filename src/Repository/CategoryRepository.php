<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class CategoryRepository extends EntityRepository
{
    /**
     * Returns all the categories corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.name) LIKE :term')
            ->andWhere('c.suppressed = 0')
            ->orderBy('c.name', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }

    /**
     * Returns the category if not suppressed and active
     */
    public function findOneById($categoryId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.categoryId = :categoryId')
            ->andWhere('c.suppressed = 0')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findActiveProducts() {
        return $this->createQueryBuilder('c')
        ->innerJoin('c.products', 'link')
        ->innerJoin('link.product', 'p')
        ->where('c.suppressed = 0')
        ->andWhere('p.visibility <> \'archived\'')
        ->andWhere('p.suppressed = 0')
        ->orderBy('c.orderItem','ASC')
        ->getQuery()
        ->getResult()
        ;
    }

}
