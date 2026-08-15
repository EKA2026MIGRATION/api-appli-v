<?php

namespace App\Service;

use App\Entity\Location;

/**
 * LocationServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface LocationServiceInterface
{
    /**
     * Creates the location
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the location as deleted
     * @return array
     */
    public function delete(Location $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Location $object);

    /**
     * Modifies the location
     * @return array
     */
    public function modify(Location $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Location $object);
}
