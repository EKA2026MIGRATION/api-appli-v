<?php

namespace App\Service;

use App\Entity\Category;

/**
 * CategoryServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface CategoryServiceInterface
{
    /**
     * Creates the category
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the category as deleted
     * @return array
     */
    public function delete(Category $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Category $object);

    /**
     * Modifies the category
     * @return array
     */
    public function modify(Category $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Category $object);
}
