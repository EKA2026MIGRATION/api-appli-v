<?php

namespace App\Service;

use App\Entity\Sport;

/**
 * SportServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface SportServiceInterface
{
    /**
     * Creates the sport
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the sport as deleted
     * @return array
     */
    public function delete(Sport $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Sport $object);

    /**
     * Modifies the sport
     * @return array
     */
    public function modify(Sport $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Sport $object);
}
