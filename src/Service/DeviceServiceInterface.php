<?php

namespace App\Service;

use App\Entity\Device;

/**
 * DeviceServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface DeviceServiceInterface
{

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Device $object);
}
