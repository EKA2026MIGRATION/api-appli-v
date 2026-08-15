<?php

namespace App\Service;

use App\Entity\CallTwilio;

/**
 * CallTwilioServiceInterface class
 * @author Sandy
 */
interface CallTwilioServiceInterface
{
    /**
     * Creates the category
     * @return array
     */
    public function create(string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(CallTwilio $object);
}
