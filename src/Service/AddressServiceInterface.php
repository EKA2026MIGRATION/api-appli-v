<?php

namespace App\Service;

use App\Entity\Address;

/**
 * AddressServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface AddressServiceInterface
{
    /**
     * Creates the address
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the address as deleted
     * @return array
     */
    public function delete(Address $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Address $object);

    /**
     * Modifies the address
     * @return array
     */
    public function modify(Address $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Address $object);
}
