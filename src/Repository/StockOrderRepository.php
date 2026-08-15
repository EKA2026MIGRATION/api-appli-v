<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * StockOrderRepository class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StockOrderRepository extends EntityRepository
{


    /**
     * Returns all list of order
     */
    public function findList()
    {
        return $this->createQueryBuilder('s')
            ->select('s.dateOrder, s.id')
            ->orderBy('s.dateOrder', 'ASC')
            ->groupBy('s.dateOrder')
            ->getQuery()
            ->getResult()
            ;
    }
}
