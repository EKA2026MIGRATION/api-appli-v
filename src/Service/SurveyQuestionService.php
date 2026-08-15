<?php

namespace App\Service;

use App\Entity\SurveyQuestion;
use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * SurveyQuestionService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class SurveyQuestionService implements SurveyQuestionServiceInterface
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

        if(isset($data['chapterId']) || isset($data['chapter_id'])) {
            isset($data['chapterId']) ? $chapterId = $data['chapterId'] : $chapterId = $data['chapter_id'];
            if(!$chapter = $this->em->getRepository('App\Entity\SurveyChapter')->find($chapterId))  return ['message' => 'no chapter found'];
        } else {
            return ['message' => 'chapter_id is missing'];
        }

        //Submits data
        $object = new SurveyQuestion();
        $object->setChapter($chapter);

        $this->mainService->hydrate($object, $data);


        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->create($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'SurveyQuestion ajouté',
            'SurveyQuestion' => $this->mainService->toArray($object->toArray()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SurveyQuestion $object)
    {
       
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'SurveyQuestion supprimé',
        );
    }


    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(SurveyQuestion $object)
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
    public function modify(SurveyQuestion $object, string $data)
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
    public function toArray(SurveyQuestion $object)
    {
        //Main data
        $objectArray = $object->toArray();
        return $objectArray;
    }
}
