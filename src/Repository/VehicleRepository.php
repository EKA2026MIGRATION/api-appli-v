<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * VehicleRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class VehicleRepository extends EntityRepository
{
    /**
     * Returns all the vehicles in an array
     */
    public function findAllInArray()
    {
        return $this->createQueryBuilder('v')
            ->where('v.suppressed = 0')
            ->orderBy('v.matriculation', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /**
     * Returns the vehicle if not suppressed
     */
    public function findOneById($vehicleId)
    {
        return $this->createQueryBuilder('v')
            ->where('v.vehicleId = :vehicleId')
            ->andWhere('v.suppressed = 0')
            ->setParameter('vehicleId', $vehicleId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
