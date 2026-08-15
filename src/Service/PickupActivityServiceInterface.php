<?php

namespace App\Service;

use App\Entity\PickupActivity;

/**
 * PickupActivityServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface PickupActivityServiceInterface
{
    /**
     * Creates the pickupActivity
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the pickupActivity as deleted
     * @return array
     */
    public function delete(PickupActivity $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(PickupActivity $object);

    /**
     * Modifies the pickupActivity
     * @return array
     */
    public function modify(PickupActivity $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(PickupActivity $object);
}
