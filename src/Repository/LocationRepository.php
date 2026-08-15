<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LocationRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class LocationRepository extends EntityRepository
{
    /**
     * Returns all the locations in an array
     */
    public function findAll($group_front = null)
    {
        if($group_front) {
            return $this->createQueryBuilder('l')
                ->where('l.suppressed = 0')
                ->andWhere('l.groupFront = :group_front')
                ->orderBy('l.name', 'ASC')
                ->setParameter('group_front', $group_front)
                ->getQuery()
                ;
        }

        return $this->createQueryBuilder('l')
            ->where('l.suppressed = 0')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
        ;
    }


    /**
     * Returns the location if not suppressed
     */
    public function findOneById($locationId)
    {
        return $this->createQueryBuilder('l')
            ->where('l.locationId = :locationId')
            ->andWhere('l.suppressed = 0')
            ->setParameter('locationId', $locationId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
