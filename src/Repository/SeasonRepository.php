<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SeasonRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SeasonRepository extends EntityRepository
{
    /**
     * Returns all the seasons in an array
     */
    public function findAllByStatus($status)
    {
        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->andWhere('s.suppressed = 0')
            ->orderBy('s.name', 'ASC')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the season if not suppressed and active
     */
    public function findOneById($seasonId)
    {
        return $this->createQueryBuilder('s')
            ->where('s.seasonId = :seasonId')
            ->andWhere('s.suppressed = 0')
            ->setParameter('seasonId', $seasonId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns the season found by dateStart if not suppressed
     */
    public function findOneByDateStart($dateStart)
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateStart = :dateStart')
            ->andWhere('s.suppressed = 0')
            ->setParameter('dateStart', $dateStart)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneIncludeMonth($dateStart)
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateStart <= :dateStart')
            ->andWhere('s.dateEnd >= :dateStart')
            ->andWhere('s.suppressed = 0')
            ->setParameter('dateStart', $dateStart)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
