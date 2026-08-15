<?php

namespace App\Service;

use App\Entity\ExtractList;

/**
 * ExtractListServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface ExtractListServiceInterface
{
    /**
     * Creates the list
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the list as deleted
     * @return array
     */
    public function delete(ExtractList $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(ExtractList $object);

    /**
     * Modifies the list
     * @return array
     */
    public function modify(ExtractList $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(ExtractList $object);
}
