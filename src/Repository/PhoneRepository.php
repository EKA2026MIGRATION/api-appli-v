<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PhoneRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PhoneRepository extends EntityRepository
{
    /**
     * Returns the phone if not suppressed
     */
    public function findOneById($phoneId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.phoneId = :phoneId')
            ->andWhere('p.suppressed = 0')
            ->setParameter('phoneId', $phoneId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns the phones like the number
     */
    public function findLike($number)
    {
        return $this->createQueryBuilder('p')
            ->where('p.phone like :number')
            ->andWhere('p.suppressed = 0')
            ->setParameter('number', '%'.$number.'%')
            ->getQuery()
            ->getResult()
        ;
    }
}
