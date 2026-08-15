<?php

namespace App\Service;

use App\Entity\Component;

/**
 * ComponentServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface ComponentServiceInterface
{
    /**
     * Creates the component
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the component as deleted
     * @return array
     */
    public function delete(Component $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Component $object);

    /**
     * Modifies the component
     * @return array
     */
    public function modify(Component $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Component $object);
}
