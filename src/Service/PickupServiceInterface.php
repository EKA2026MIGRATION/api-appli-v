<?php

namespace App\Service;

use App\Entity\Pickup;

/**
 * PickupServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface PickupServiceInterface
{
    /**
     * Creates the pickup
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the pickup as deleted
     * @return array
     */
    public function delete(Pickup $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Pickup $object);

    /**
     * Modifies the pickup
     * @return array
     */
    public function modify(Pickup $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Pickup $object);
}
