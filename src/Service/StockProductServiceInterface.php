<?php

namespace App\Service;

use App\Entity\StockProduct;

/**
 * StockProductServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface StockProductServiceInterface
{

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(StockProduct $object);
}
