<?php

namespace App\Service;

use App\Entity\SurveyChapter;

/**
 * SurveyChapterServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface SurveyChapterServiceInterface
{
    /**
     * Creates the SurveyChapter
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the Survey as deleted
     * @return array
     */
    public function delete(SurveyChapter $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(SurveyChapter $object);

    /**
     * Modifies the Survey
     * @return array
     */
    public function modify(SurveyChapter $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(SurveyChapter $object);
}
