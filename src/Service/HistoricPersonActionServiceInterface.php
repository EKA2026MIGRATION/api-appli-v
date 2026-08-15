<?php

namespace App\Service;

use App\Entity\HistoricPersonAction;

/**
 * HistoricPersonActionServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface HistoricPersonActionServiceInterface
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
    public function delete(HistoricPersonAction $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(HistoricPersonAction $object);


    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(HistoricPersonAction $object);
}
