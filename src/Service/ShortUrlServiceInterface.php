<?php

namespace App\Service;

use App\Entity\ShortUrl;

/**
 * ShortUrlInterface class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
interface ShortUrlServiceInterface
{
    /**
     * Creates the short url
     * @return array
     */
    public function create(string $data);


    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(ShortUrl $object);
}
