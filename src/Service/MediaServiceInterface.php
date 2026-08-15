<?php

namespace App\Service;

use App\Entity\Media;

/**
 * MediaServiceInterface class
 * @author Sandy Razafitrimo
 */
interface MediaServiceInterface
{
    /**
     * Creates the Media
     * @return array
     */
    public function create(string $data);

    /**
     * Modifies the Media
     * @return array
     */
    public function modify(Media $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Media $object);
}
