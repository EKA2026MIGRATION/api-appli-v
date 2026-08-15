<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TelevisionRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class TelevisionRepository extends EntityRepository
{
    /**
     * Returns all the television
     */
    public function findAll()
    {
        return $this->createQueryBuilder('t')
            ->where('t.suppressed = 0')
            ->orderBy('t.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the television if not suppressed
     */
    public function findOneById($televisionId)
    {
        return $this->createQueryBuilder('t')
            ->where('t.televisionId = :televisionId')
            ->andWhere('t.suppressed = 0')
            ->setParameter('televisionId', $televisionId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
