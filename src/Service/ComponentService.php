<?php

namespace App\Service;

use App\Entity\Component;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * ComponentService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ComponentService implements ComponentServiceInterface
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
        $object = new Component();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'component-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Composant ajouté',
            'component' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Component $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Composant supprimé',
        );
    }

    /**
     * Returns the list of all components in the array format
     * @return array
     */
    public function findAll()
    {
        return $this->em
            ->getRepository('App\Entity\Component')
            ->findAll()
        ;
    }

    /**
     * Searches the term in the Component collection
     * @return array
     */
    public function findAllSearch(string $term)
    {
        return $this->em
            ->getRepository('App\Entity\Component')
            ->findAllSearch($term)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Component $object)
    {
        if (null === $object->getNameFr() ||
            null === $object->getVat()) {
            throw new UnprocessableEntityHttpException('Missing data for Component -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Component $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'component-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Composant modifié',
            'component' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Component $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
