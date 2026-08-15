<?php

namespace App\Service;

use App\Entity\BookletBoard;

/**
 * BookletBoardServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface BookletBoardServiceInterface
{
    /**
     * Creates the BookletBoard
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the Survey as deleted
     * @return array
     */
    public function delete(BookletBoard $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(BookletBoard $object);

    /**
     * Modifies the Survey
     * @return array
     */
    public function modify(BookletBoard $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(BookletBoard $object);
}
