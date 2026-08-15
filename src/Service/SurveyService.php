<?php

namespace App\Service;

use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * LocationService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class SurveyService implements SurveyServiceInterface
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

    public function display($survey) {
        return $this->mainService->toArray($this->toArray($survey));
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $data = json_decode($data, true);

        //Submits data
        $object = new Survey();

        $this->mainService->hydrate($object, $data);


        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->create($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Survey ajouté',
            'survey' => $this->mainService->toArray($this->toArray($object)),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Survey $object)
    {
       
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Survey supprimé',
        );
    }

    /**
     * Returns the list of all persons in the array format
     * @return array
     */
    public function findAll()
    {

        $surveys = $this->em->getRepository('App\Entity\Survey')->findBy(['isActive' => 1], ['name' => 'ASC']);

        foreach($surveys as $survey) {
            $results[] = $this->mainService->toArray($this->toArray($survey));
        }


        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Survey $object)
    {
        if (
            null === $object->getName()
            ) {
            throw new UnprocessableEntityHttpException('Missing data for Survey -> ' . json_encode($this->toArray($object)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Survey $object, string $data)
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
            'survey' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Survey $object)
    {
        //Main data
        $objectArray = $object->toArray();

        if($objectArray['chapters'] !== null) {
            $chaptersArray = [];
            foreach($object->getChapters() as $chapter) {
                if($chapter->getSuppressed() == 1) continue;
                $chaptersArray[] = $this->mainService->toArray($chapter->toArray());
            }

            $objectArray['chapters'] = $chaptersArray;
        }

        return $objectArray;
    }
}