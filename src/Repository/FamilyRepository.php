<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FamilyRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class FamilyRepository extends EntityRepository
{
    /**
     * Returns all the families in an array
     */
    public function findAll()
    {
        return $this->createQueryBuilder('f')
            ->where('f.suppressed = 0')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * Returns all the families corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('f')
            ->where('LOWER(f.name) LIKE :term')
            ->andWhere('f.suppressed = 0')
            ->orderBy('f.name', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }

    /**
     * Returns the family if not suppressed
     */
    public function findOneById($familyId)
    {
        return $this->createQueryBuilder('f')
            ->where('f.familyId = :familyId')
            ->andWhere('f.suppressed = 0')
            ->setParameter('familyId', $familyId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
