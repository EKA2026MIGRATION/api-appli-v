<?php

namespace App\Service;

use App\Entity\Family;

/**
 * FamilyServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface FamilyServiceInterface
{
    /**
     * Creates the family
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the family as deleted
     * @return array
     */
    public function delete(Family $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Family $object);

    /**
     * Modifies the family
     * @return array
     */
    public function modify(Family $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Family $object);
}
