<?php

namespace App\Service;

use App\Entity\BookletBoard;
use App\Entity\Booklet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * BookletBoardService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletBoardService implements BookletBoardServiceInterface
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

        if(isset($data['bookletId']) || isset($data['booklet_id'])) {
            isset($data['bookletId']) ? $bookletId = $data['bookletId'] : $bookletId = $data['booklet_id'];
            if(!$booklet = $this->em->getRepository('App\Entity\Booklet')->find($bookletId))  return ['message' => 'no booklet found'];
        } else {
            return ['message' => 'booklet_id is missing'];
        }

        //Submits data
        $object = new BookletBoard();
        $object->setBooklet($booklet);

        $this->mainService->hydrate($object, $data);


        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->create($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'BookletBoard ajouté',
            'BookletBoard' => $this->mainService->toArray($object->toArray()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(BookletBoard $object)
    {
       
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'BookletBoard supprimé',
        );
    }


    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(BookletBoard $object)
    {
        if (
            null === $object->getName()
            ) {
            throw new UnprocessableEntityHttpException('Missing data for BookletBoard -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(BookletBoard $object, string $data)
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
            'BookletBoard' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(BookletBoard $object)
    {
        //Main data
        $objectArray = $object->toArray();
        return $objectArray;
    }
}
