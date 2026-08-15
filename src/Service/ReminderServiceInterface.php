<?php

namespace App\Service;

use App\Entity\Reminder;

/**
 * ReminderServiceInterface class
 * @author Sandy Razafitrimo
 */
interface ReminderServiceInterface
{
    /**
     * Creates the ride
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the ride as deleted
     * @return array
     */
    public function delete(Reminder $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Reminder $object);

    /**
     * Modifies the ride
     * @return array
     */
    public function modify(Reminder $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Reminder $object);
}
