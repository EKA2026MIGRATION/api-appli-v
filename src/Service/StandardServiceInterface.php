<?php

namespace App\Service;

use App\Entity\Standard;

/**
 * StandardServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface StandardServiceInterface
{

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Standard $object);
}
