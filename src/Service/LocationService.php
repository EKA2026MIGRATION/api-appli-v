<?php

namespace App\Service;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * LocationService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class LocationService implements LocationServiceInterface
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
        $object = new Location();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'location-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Lieu ajouté',
            'location' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Location $object)
    {
        //Removes links to products
        if (!$object->getProducts()->isEmpty()) {
            foreach ($object->getProducts() as $productLink) {
                $this->em->remove($productLink);
            }
        }

        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Lieu supprimé',
        );
    }

    /**
     * Returns the list of all persons in the array format
     * @return array
     */
    public function findAll($group_front = null)
    {
        return $this->em
            ->getRepository('App\Entity\Location')
            ->findAll($group_front)
        ;
    }


    public function updateVisibility($location, $front_visibility) {
        $location->setFrontVisibility($front_visibility);
        $this->em->persist($location);
        $this->em->flush();

        return $location->toArray();

    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Location $object)
    {
        if (null === $object->getName() ||
            null === $object->getAddress()) {
            throw new UnprocessableEntityHttpException('Missing data for Location -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Location $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'location-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Persists in DB
        $this->em->flush();
        $this->em->refresh($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Lieu modifié',
            'location' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Location $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
