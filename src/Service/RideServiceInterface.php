<?php

namespace App\Service;

use App\Entity\Ride;

/**
 * RideServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface RideServiceInterface
{
    /**
     * Creates the ride
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the ride as deleted
     * @return array
     */
    public function delete(Ride $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Ride $object);

    /**
     * Modifies the ride
     * @return array
     */
    public function modify(Ride $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Ride $object);
}
