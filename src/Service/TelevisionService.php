<?php

namespace App\Service;

use App\Entity\Television;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * TelevisionService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class TelevisionService implements TelevisionServiceInterface
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
     * Adds specific data that could not be added via generic method
     */
    public function addSpecificData(Television $object, array $data)
    {
        //Should be done from TelevisionType but it returns null...
        if (array_key_exists('start', $data)) {
            $object->setStart(DateTime::createFromFormat('H:i:s', $data['start']));
        }
        if (array_key_exists('end', $data)) {
            $object->setEnd(DateTime::createFromFormat('H:i:s', $data['end']));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new Television();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'television-create', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Télévision ajoutée',
            'television' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Television $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Television supprimée',
        );
    }

    /**
     * Returns the list of all television
     * @return array
     */
    public function findAll()
    {
        return $this->em
            ->getRepository('App\Entity\Television')
            ->findAll()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Television $object)
    {
        if (null === $object->getStart() ||
            null === $object->getEnd() ||
            null === $object->getModule()) {
            throw new UnprocessableEntityHttpException('Missing data for Television -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Television $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'television-modify', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Television modifiée',
            'television' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Television $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
