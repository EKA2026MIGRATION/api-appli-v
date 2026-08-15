<?php

namespace App\Service;

use App\Entity\Sport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * SportService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SportService implements SportServiceInterface
{
    private $em;

    private $mainService;

    private $productService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        ProductServiceInterface $productService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->productService = $productService;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new Sport();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'sport-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Sport ajouté',
            'sport' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Sport $object)
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
            'message' => 'Sport supprimé',
        );
    }

    /**
     * Returns the list of all families in the array format
     * @return array
     */
    public function findAll()
    {
        return $this->em
            ->getRepository('App\Entity\Sport')
            ->findAll()
        ;
    }

    /**
     * Searches the term in the Sport collection
     * @return array
     */
    public function findAllSearch(string $term)
    {
        return $this->em
            ->getRepository('App\Entity\Sport')
            ->findAllSearch($term)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Sport $object)
    {
        if (null === $object->getName()) {
            throw new UnprocessableEntityHttpException('Missing data for Sport -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Sport $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'sport-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Sport modifié',
            'sport' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Sport $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related products
        if (null !== $object->getProducts()) {
            $products = array();
            foreach($object->getProducts() as $productLink) {
                if (!$productLink->getProduct()->getSuppressed()) {
                    $products[] = $this->mainService->toArray($productLink->getProduct()->toArray());
                }
            }
            $objectArray['products'] = $products;
        }

        return $objectArray;
    }
}
