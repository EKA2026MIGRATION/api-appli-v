<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AddressRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class AddressRepository extends EntityRepository
{
    /**
     * Returns the addresses to geocode
     */
    public function findGeocode()
    {
        return $this->createQueryBuilder('a')
            ->where('a.suppressed = 0')
            ->andWhere('a.address IS NOT NULL')
            ->andWhere('a.address != :empty')
            ->andWhere('a.town IS NOT NULL')
            ->andWhere('a.town != :empty')
            ->andWhere('a.latitude IS NULL OR a.longitude IS NULL')
            ->setParameter('empty', '')
            ->orderBy('a.addressId', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the address if not suppressed
     */
    public function findOneById($addressId)
    {
        return $this->createQueryBuilder('a')
            ->where('a.addressId = :addressId')
            ->andWhere('a.suppressed = 0')
            ->setParameter('addressId', $addressId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
