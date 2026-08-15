<?php

namespace App\Service;

use App\Entity\School;

/**
 * SchoolServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface SchoolServiceInterface
{
    /**
     * Creates the school
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the school as deleted
     * @return array
     */
    public function delete(School $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(School $object);

    /**
     * Modifies the school
     * @return array
     */
    public function modify(School $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(School $object);
}
