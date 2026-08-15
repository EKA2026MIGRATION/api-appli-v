<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * InvoiceProductRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class InvoiceProductRepository extends EntityRepository
{
    /**
     * Returns all the persons corresponding to the searched term
     */
    public function findByDate($dateStart, $dateEnd)
    {

        return $this->createQueryBuilder('ip')
            ->leftJoin('ip.invoice', 'i')
            ->select('ip.nameFr as name, ip.priceTtc')
            ->where('i.date BETWEEN :dateStart AND :dateEnd')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->getQuery()
            ->getScalarResult()
        ;
    }
}
