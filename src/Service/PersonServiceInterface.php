<?php

namespace App\Service;

use App\Entity\Person;

/**
 * PersonServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface PersonServiceInterface
{
    /**
     * Creates the person
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the person as deleted
     * @return array
     */
    public function delete(Person $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Person $object);

    /**
     * Modifies the person
     * @return array
     */
    public function modify(Person $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Person $object);

    /**
     * Searches persons by email including soft-deleted
     * @return array
     */
    public function searchByEmail(string $email);

    /**
     * Renames email to email_old to unblock duplicate
     * @return array
     */
    public function freeEmail(int $personId);
}
