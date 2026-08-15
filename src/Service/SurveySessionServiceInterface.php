<?php

namespace App\Service;

use App\Entity\SurveySession;

/**
 * SurveySessionInterface class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
interface SurveySessionServiceInterface
{
    /**
     * Creates the SurveySession
     * @return array
     */
    public function create(string $data);


    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(SurveySession $object);
}
