<?php

namespace App\Service;

use App\Entity\Rdv;

/**
 * RdvServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface RdvServiceInterface
{

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Rdv $object);
}
