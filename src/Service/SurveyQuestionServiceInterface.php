<?php

namespace App\Service;

use App\Entity\SurveyQuestion;

/**
 * SurveyQuestionServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface SurveyQuestionServiceInterface
{
    /**
     * Creates the SurveyQuestion
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the SurveyQuestion as deleted
     * @return array
     */
    public function delete(SurveyQuestion $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(SurveyQuestion $object);

    /**
     * Modifies the SurveyQuestion
     * @return array
     */
    public function modify(SurveyQuestion $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(SurveyQuestion $object);
}
