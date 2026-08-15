<?php

namespace App\Service;

use App\Entity\Meal;

/**
 * MealServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface MealServiceInterface
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
    public function delete(Meal $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Meal $object);

    /**
     * Modifies the ride
     * @return array
     */
    public function modify(Meal $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Meal $object);
}
