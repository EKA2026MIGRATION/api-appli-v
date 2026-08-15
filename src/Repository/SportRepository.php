<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SportRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SportRepository extends EntityRepository
{
    /**
     * Returns all the sports corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('s')
            ->where('LOWER(s.name) LIKE :term')
            ->andWhere('s.suppressed = 0')
            ->orderBy('s.name', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }

    /**
     * Returns the sport if not suppressed and active
     */
    public function findOneById($sportId)
    {
        return $this->createQueryBuilder('s')
            ->where('s.sportId = :sportId')
            ->andWhere('s.suppressed = 0')
            ->setParameter('sportId', $sportId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
