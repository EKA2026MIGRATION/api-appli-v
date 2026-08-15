<?php

namespace App\Service;

use App\Entity\HistoricSmsList;

/**
 * ExtractListServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface HistoricSmsListServiceInterface
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
    public function delete(HistoricSmsList $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(HistoricSmsList $object);

    /**
     * Modifies the list
     * @return array
     */
    public function modify(HistoricSmsList $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(HistoricSmsList $object);
}
