<?php

namespace App\Service;

use App\Entity\BookletChild;

/**
 * BookletChildServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface BookletChildServiceInterface
{
    /**
     * Creates the BookletChild
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the BookletChild as deleted
     * @return array
     */
    public function delete(BookletChild $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(BookletChild $object);

    /**
     * Modifies the BookletChild
     * @return array
     */
    public function modify(BookletChild $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(BookletChild $object);
}
