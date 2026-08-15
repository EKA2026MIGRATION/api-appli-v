<?php

namespace App\Service;

use App\Entity\ProductCancelledDate;

/**
 * ProductCancelledDateServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface ProductCancelledDateServiceInterface
{
    /**
     * Creates the productCancelledDate
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the productCancelledDate as deleted
     * @return array
     */
    public function delete(ProductCancelledDate $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(ProductCancelledDate $object);

    /**
     * Modifies the productCancelledDate
     * @return array
     */
    public function modify(ProductCancelledDate $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(ProductCancelledDate $object);
}
