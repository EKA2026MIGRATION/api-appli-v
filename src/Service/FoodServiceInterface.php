<?php

namespace App\Service;

use App\Entity\Food;

/**
 * FoodServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface FoodServiceInterface
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
    public function delete(Food $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Food $object);

    /**
     * Modifies the ride
     * @return array
     */
    public function modify(Food $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Food $object);
}
