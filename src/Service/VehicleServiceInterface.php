<?php

namespace App\Service;

use App\Entity\Vehicle;

/**
 * VehicleServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface VehicleServiceInterface
{
    /**
     * Creates the vehicle
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the vehicle as deleted
     * @return array
     */
    public function delete(Vehicle $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Vehicle $object);

    /**
     * Modifies the vehicle
     * @return array
     */
    public function modify(Vehicle $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Vehicle $object);
}
