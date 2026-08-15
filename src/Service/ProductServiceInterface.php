<?php

namespace App\Service;

use App\Entity\Product;

/**
 * ProductServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface ProductServiceInterface
{
    /**
     * Creates the product
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the product as deleted
     * @return array
     */
    public function delete(Product $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Product $object);

    /**
     * Modifies the product
     * @return array
     */
    public function modify(Product $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Product $object);
}
