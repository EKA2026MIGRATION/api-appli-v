<?php

namespace App\Service;

use App\Entity\Notification;

/**
 * NotificationServiceInterface class
 * @author Sandy Razafitrimo
 */
interface NotificationServiceInterface
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
    public function delete(Notification $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Notification $object);

    /**
     * Modifies the ride
     * @return array
     */
    public function modify(Notification $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Notification $object);
}
