<?php

namespace App\Service;

use App\Entity\Booklet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * BookletService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletService implements BookletServiceInterface
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

    public function display($booklet) {
        return $this->mainService->toArray($this->toArray($booklet));
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $data = json_decode($data, true);

        //Submits data
        $object = new Booklet();

        $this->mainService->hydrate($object, $data);


        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->create($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Booklet ajouté',
            'booklet' => $this->mainService->toArray($this->toArray($object)),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Booklet $object)
    {
       
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Booklet supprimé',
        );
    }


    /**
     * Returns the list of all persons in the array format
     * @return array
     */
    public function findAll()
    {

        $booklets = $this->em->getRepository('App\Entity\Booklet')->findBy(['isActive' => 1], ['name' => 'ASC']);

        foreach($booklets as $booklet) {
            $results[] = $this->mainService->toArray($this->toArray($booklet));
        }


        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Booklet $object)
    {
        if (
            null === $object->getName()
            ) {
            throw new UnprocessableEntityHttpException('Missing data for Booklet -> ' . json_encode($this->toArray($object)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Booklet $object, string $data)
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
            'message' => 'Booklet modifié',
            'booklet' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Booklet $object)
    {
        //Main data
        $objectArray = $object->toArray();

        if($objectArray['boards'] !== null) {
            $boardsArray = [];
            foreach($object->getBoards() as $board) {
                if($board->getSuppressed() == 1) continue;
                $boardsArray[] = $this->mainService->toArray($board->toArray());
            }

            $objectArray['boards'] = $boardsArray;
        }

        return $objectArray;
    }
}