<?php

namespace App\Service;

use App\Entity\BookletItem;
use App\Entity\Booklet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * BookletItemService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletItemService implements BookletItemServiceInterface
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

        if(isset($data['boardId']) || isset($data['board_id'])) {
            isset($data['boardId']) ? $boardId = $data['boardId'] : $boardId = $data['board_id'];
            if(!$board = $this->em->getRepository('App\Entity\BookletBoard')->find($boardId))  return ['message' => 'no board found'];
        } else {
            return ['message' => 'board_id is missing'];
        }

        //Submits data
        $object = new BookletItem();
        $object->setBoard($board);

        $this->mainService->hydrate($object, $data);


        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->create($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'BookletItem ajouté',
            'BookletItem' => $this->mainService->toArray($object->toArray()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(BookletItem $object)
    {
       
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'BookletItem supprimé',
        );
    }


    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(BookletItem $object)
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
    public function modify(BookletItem $object, string $data)
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
    public function toArray(BookletItem $object)
    {
        //Main data
        $objectArray = $object->toArray();
        return $objectArray;
    }
}
