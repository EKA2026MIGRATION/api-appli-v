<?php

namespace App\Service;

use App\Entity\Survey;

/**
 * SurveyServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface SurveyServiceInterface
{
    /**
     * Creates the Survey
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the Survey as deleted
     * @return array
     */
    public function delete(Survey $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Survey $object);

    /**
     * Modifies the Survey
     * @return array
     */
    public function modify(Survey $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Survey $object);
}
