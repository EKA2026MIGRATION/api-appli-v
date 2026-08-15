<?php

namespace App\Service;

use App\Entity\Registration;

/**
 * RegistrationServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface RegistrationServiceInterface
{
    /**
     * Creates the registration
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the registration as deleted
     * @return array
     */
    public function delete(Registration $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Registration $object);

    /**
     * Modifies the registration
     * @return array
     */
    public function modify(Registration $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Registration $object);
}
