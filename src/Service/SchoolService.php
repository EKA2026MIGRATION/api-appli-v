<?php

namespace App\Service;

use App\Entity\School;
use App\Entity\Person;
use App\Entity\PersonSchoolLink;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * SchoolService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SchoolService implements SchoolServiceInterface
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
        //Submits data
        $object = new School();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'school-create', $data);

        //Checks if school not already exists
        $school = null;
        if (array_key_exists('googlePlaceId', $data)) {
            $school = $this->em->getRepository('App\Entity\School')->findOneByGooglePlaceId($data['googlePlaceId']);
        }

        //Persist new school
        if (null === $school) {
            //Checks if entity has been filled
            $this->isEntityFilled($object);

            //Persists data
            $this->mainService->persist($object);
        //Otherwise returns existing school
        } else {
            $object = $school;
        }

        //Returns data
        return array(
            'status' => true,
            'message' => 'École ajoutée',
            'school' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(School $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'École supprimée',
        );
    }

    /**
     * Returns the list of all schools in the array format
     */
    public function findAll()
    {
        return $this->em
            ->getRepository('App\Entity\School')
            ->findAll()
        ;
    }

    /**
     * Searches the term in the School collection
     * @return array
     */
    public function findAllSearch(string $term)
    {
        return $this->em
            ->getRepository('App\Entity\School')
            ->findAllSearch($term)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(School $object)
    {
        if (null === $object->getName() ||
            null === $object->getAddress() ||
            null === $object->getPostal() ||
            null === $object->getTown()) {
            throw new UnprocessableEntityHttpException('Missing data for School -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(School $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'school-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'École modifiée',
            'school' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(School $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
