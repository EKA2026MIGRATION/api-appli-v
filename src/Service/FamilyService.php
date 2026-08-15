<?php

namespace App\Service;

use App\Entity\Family;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * FamilyService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class FamilyService implements FamilyServiceInterface
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
        $object = new Family();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'family-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Famille ajoutée',
            'family' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Family $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Famille supprimée',
        );
    }

    /**
     * Returns the list of all families in the array format
     * @return array
     */
    public function findAll()
    {
        return $this->em
            ->getRepository('App\Entity\Family')
            ->findAll()
        ;
    }

    /**
     * Returns the list of all families in the array format
     * @return array
     */
    public function findBy()
    {
        $familys = $this->em->getRepository('App\Entity\Family')->findBy([]);

        $familysArray = [];

        foreach($familys as $family) {
            $familysArray[] = $family->toArray();
        }

        return $familysArray;
        
    }

    /**
     * Searches the term in the Family collection
     * @return array
     */
    public function findAllSearch(string $term)
    {
        return $this->em
            ->getRepository('App\Entity\Family')
            ->findAllSearch($term)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Family $object)
    {
        if (null === $object->getName()) {
            throw new UnprocessableEntityHttpException('Missing data for Family -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Family $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'family-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Famille modifiée',
            'family' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Family $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related products
        if (null !== $object->getProducts()) {
            $products = array();
            foreach($object->getProducts() as $product) {
                if (!$product->getSuppressed()) {
                    $products[] = $this->productService->toArray($product);
                }
            }
            $objectArray['products'] = $products;
        }

        return $objectArray;
    }
}
