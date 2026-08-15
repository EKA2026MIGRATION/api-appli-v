<?php

namespace App\Service;

use App\Entity\ChildPresence;

/**
 * ChildPresenceServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface ChildPresenceServiceInterface
{
    /**
     * Creates the childPresence
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the childPresence as deleted
     * @return array
     */
    public function delete(ChildPresence $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(ChildPresence $object);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(ChildPresence $object);
}
