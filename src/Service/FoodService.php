<?php

namespace App\Service;

use App\Entity\Food;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * FoodService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class FoodService implements FoodServiceInterface
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
        $object = new Food();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'food-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Aliment ajouté',
            'food' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Food $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Aliment supprimé',
        );
    }

    /**
     * Returns all the foods active
     * @return array
     */
    public function findAllByStatus($status)
    {
        return $this->em
            ->getRepository('App\Entity\Food')
            ->findAllByStatus($status)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Food $object)
    {
        if (null === $object->getName() ||
            null === $object->getKind()) {
            throw new UnprocessableEntityHttpException('Missing data for Food -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Food $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'food-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Aliment modifié',
            'food' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Food $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
