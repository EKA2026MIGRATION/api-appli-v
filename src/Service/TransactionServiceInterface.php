<?php

namespace App\Service;

use App\Entity\Transaction;

/**
 * TransactionServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface TransactionServiceInterface
{
    /**
     * Creates the transaction
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the transaction as deleted
     * @return array
     */
    public function delete(Transaction $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Transaction $object);

    /**
     * Modifies the transaction
     * @return array
     */
    public function modify(Transaction $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Transaction $object);
}
