<?php

namespace App\Service;

use App\Entity\SurveyStaffNotation;

/**
 * SurveyStaffNotationServiceInterface class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
interface SurveyStaffNotationServiceInterface
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
    public function toArray(SurveyStaffNotation $object);
}
