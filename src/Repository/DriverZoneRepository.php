<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DriverZoneRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class DriverZoneRepository extends EntityRepository
{
    /**
     * Returns the maxmium number of DriverZones
     * @return int
     */
    public function getMaxDriverZones()
    {
        return (int) $this->createQueryBuilder('z')
            ->select('COUNT(DISTINCT z.postal) AS MaxZones')
            ->groupBy('z.staff')
            ->where('z.suppressed = 0')
            ->orderBy('MaxZones', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
