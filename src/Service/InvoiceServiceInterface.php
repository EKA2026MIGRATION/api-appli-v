<?php

namespace App\Service;

use App\Entity\Invoice;

/**
 * InvoiceServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface InvoiceServiceInterface
{
    /**
     * Creates the invoice
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the invoice as deleted
     * @return array
     */
    public function delete(Invoice $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Invoice $object);

    /**
     * Modifies the invoice
     * @return array
     */
    public function modify(Invoice $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Invoice $object);
}
