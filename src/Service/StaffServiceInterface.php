<?php

namespace App\Service;

use App\Entity\Staff;

/**
 * StaffServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface StaffServiceInterface
{
    /**
     * Creates the staff
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the staff as deleted
     * @return array
     */
    public function delete(Staff $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Staff $object);

    /**
     * Modifies the staff
     * @return array
     */
    public function modify(Staff $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Staff $object);
}
