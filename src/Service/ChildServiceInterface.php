<?php

namespace App\Service;

use App\Entity\Child;

/**
 * ChildServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface ChildServiceInterface
{
    /**
     * Creates the child
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the child as deleted
     * @return array
     */
    public function delete(Child $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Child $object);

    /**
     * Modifies the child
     * @return array
     */
    public function modify(Child $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Child $object);
}
