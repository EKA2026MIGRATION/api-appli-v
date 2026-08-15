<?php

namespace App\Service;

use App\Entity\HistoricSms;

/**
 * ExtractListServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface HistoricSmsServiceInterface
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
    public function delete(HistoricSms $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(HistoricSms $object);

    /**
     * Modifies the list
     * @return array
     */
    public function modify(HistoricSms $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(HistoricSms $object);
}
