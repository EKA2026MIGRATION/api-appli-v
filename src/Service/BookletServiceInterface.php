<?php

namespace App\Service;

use App\Entity\Booklet;

/**
 * BookletServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface BookletServiceInterface
{
    /**
     * Creates the Booklet
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the Booklet as deleted
     * @return array
     */
    public function delete(Booklet $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Booklet $object);

    /**
     * Modifies the Booklet
     * @return array
     */
    public function modify(Booklet $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Booklet $object);
}
