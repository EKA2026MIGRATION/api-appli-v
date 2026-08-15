<?php

namespace App\Service;

use App\Entity\Phone;

/**
 * PhoneServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface PhoneServiceInterface
{
    /**
     * Creates the phone
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the phone as deleted
     * @return array
     */
    public function delete(Phone $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Phone $object);

    /**
     * Modifies the phone
     * @return array
     */
    public function modify(Phone $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Phone $object);
}
