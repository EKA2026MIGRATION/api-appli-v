<?php

namespace App\Service;

use App\Entity\Week;

/**
 * WeekServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface WeekServiceInterface
{
    /**
     * Creates the week
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the week as deleted
     * @return array
     */
    public function delete(Week $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Week $object);

    /**
     * Modifies the week
     * @return array
     */
    public function modify(Week $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Week $object);
}
