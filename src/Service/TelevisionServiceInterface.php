<?php

namespace App\Service;

use App\Entity\Television;

/**
 * TelevisionServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface TelevisionServiceInterface
{
    /**
     * Creates the television
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the television as deleted
     * @return array
     */
    public function delete(Television $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Television $object);

    /**
     * Modifies the television
     * @return array
     */
    public function modify(Television $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Television $object);
}
