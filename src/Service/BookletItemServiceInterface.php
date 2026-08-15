<?php

namespace App\Service;

use App\Entity\BookletItem;

/**
 * BookletItemServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface BookletItemServiceInterface
{
    /**
     * Creates the BookletItem
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the BookletItem as deleted
     * @return array
     */
    public function delete(BookletItem $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(BookletItem $object);

    /**
     * Modifies the BookletItem
     * @return array
     */
    public function modify(BookletItem $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(BookletItem $object);
}
