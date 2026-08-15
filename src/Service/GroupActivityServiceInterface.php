<?php

namespace App\Service;

use App\Entity\GroupActivity;

/**
 * GroupActivityServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface GroupActivityServiceInterface
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
    public function delete(GroupActivity $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(GroupActivity $object);

    /**
     * Modifies the ride
     * @return array
     */
    public function modify(GroupActivity $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(GroupActivity $object);
}
