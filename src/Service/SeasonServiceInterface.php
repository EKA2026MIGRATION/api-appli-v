<?php

namespace App\Service;

use App\Entity\Season;

/**
 * SeasonServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface SeasonServiceInterface
{
    /**
     * Creates the season
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the season as deleted
     * @return array
     */
    public function delete(Season $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Season $object);

    /**
     * Modifies the season
     * @return array
     */
    public function modify(Season $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Season $object);
}
