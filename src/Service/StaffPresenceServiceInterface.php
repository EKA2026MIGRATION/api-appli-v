<?php

namespace App\Service;

use App\Entity\StaffPresence;

/**
 * StaffPresenceServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface StaffPresenceServiceInterface
{
    /**
     * Creates the staffPresence
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the staffPresence as deleted
     * @return array
     */
    public function delete(StaffPresence $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(StaffPresence $object);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(StaffPresence $object);
}
