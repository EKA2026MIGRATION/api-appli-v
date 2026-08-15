<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SchoolRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SchoolRepository extends EntityRepository
{
    /**
     * Returns all the schools in an array
     */
    public function findAll()
    {
        return $this->createQueryBuilder('s')
            ->where('s.suppressed = 0')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * Returns all the schools corresponding to the searched term
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
     * Returns the school if not suppressed
     */
    public function findOneById($schoolId)
    {
        return $this->createQueryBuilder('s')
            ->where('s.schoolId = :schoolId')
            ->andWhere('s.suppressed = 0')
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
