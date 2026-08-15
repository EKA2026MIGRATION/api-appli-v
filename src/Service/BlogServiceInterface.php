<?php

namespace App\Service;

use App\Entity\Blog;

/**
 * BlogServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface BlogServiceInterface
{
    /**
     * Creates the blog
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the blog as deleted
     * @return array
     */
    public function delete(Blog $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Blog $object);

    /**
     * Modifies the blog
     * @return array
     */
    public function modify(Blog $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Blog $object);
}
