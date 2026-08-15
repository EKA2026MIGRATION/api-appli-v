<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * WeekRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class WeekRepository extends EntityRepository
{
    /**
     * Returns all the weeks in an array
     */
    public function findAll()
    {
        return $this->createQueryBuilder('w')
            ->where('w.suppressed = 0')
            ->orderBy('w.dateStart', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * Returns all the weeks corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('w')
            ->where('LOWER(w.name) LIKE :term')
            ->andWhere('w.suppressed = 0')
            ->orderBy('w.name', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }

    /**
     * Returns the week if not suppressed
     */
    public function findOneById($weekId)
    {
        return $this->createQueryBuilder('w')
            ->where('w.weekId = :weekId')
            ->andWhere('w.suppressed = 0')
            ->setParameter('weekId', $weekId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns the week found by dateStart if not suppressed
     */
    public function findOneByDateStart($dateStart)
    {
        return $this->createQueryBuilder('w')
            ->where('w.dateStart = :dateStart')
            ->andWhere('w.suppressed = 0')
            ->setParameter('dateStart', $dateStart)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
