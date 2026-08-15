<?php

namespace App\Service;

use App\Entity\SurveyChapter;
use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * SurveyChapterService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class SurveyChapterService implements SurveyChapterServiceInterface
{
    private $em;

    private $mainService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $data = json_decode($data, true);

        if(isset($data['surveyId']) || isset($data['survey_id'])) {
            isset($data['surveyId']) ? $surveyId = $data['surveyId'] : $surveyId = $data['survey_id'];
            if(!$survey = $this->em->getRepository('App\Entity\Survey')->find($surveyId))  return ['message' => 'no survey found'];
        } else {
            return ['message' => 'survey_id is missing'];
        }

        //Submits data
        $object = new SurveyChapter();
        $object->setSurvey($survey);

        $this->mainService->hydrate($object, $data);


        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->create($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'SurveyChapter ajouté',
            'SurveyChapter' => $this->mainService->toArray($object->toArray()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SurveyChapter $object)
    {
       
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'SurveyChapter supprimé',
        );
    }


    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(SurveyChapter $object)
    {
        if (
            null === $object->getName()
            ) {
            throw new UnprocessableEntityHttpException('Missing data for SurveyChapter -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(SurveyChapter $object, string $data)
    {
        //Submits data
        $data = json_decode($data, true);

        //Submits data

        $this->mainService->hydrate($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);


        //Returns data
        return array(
            'status' => true,
            'message' => 'Survey modifié',
            'SurveyChapter' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(SurveyChapter $object)
    {
        //Main data
        $objectArray = $object->toArray();
        return $objectArray;
    }
}
