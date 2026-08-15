<?php

namespace App\Service;

use App\Entity\HistoricPersonAction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use \PDO;

/**
 * ExtractListService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class HistoricPersonActionService implements HistoricPersonActionServiceInterface
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

        $dataArray = is_array($data) ? $data : json_decode($data, true);

        $person = $this->em->getRepository('App\Entity\Person')->find($dataArray['person']);

        $historic = new HistoricPersonAction();
        $historic->setPerson($person);
        $historic->setAction($dataArray['action']);

        $this->mainService->create($historic);
        $this->mainService->persist($historic);
       
        return array(
            'status' => true,
            'message' => 'HistoricPersonAction créée',
            'historic' => $historic->toArray()
        );
    }

    public function listByAction($action)
    {

        $actions = $this->em->getRepository('App\Entity\HistoricPersonAction')->findBy(['action' => $action], ['createdAt' => 'DESC'], 100);

        foreach($actions as $action) {
            $arr[] = $action->toArray();
        }

        return $arr;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(HistoricPersonAction $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'HistoricPersonAction supprimée',
        );
    }



    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(HistoricPersonAction $object)
    {
        if (
            null === $object->getContent()) {
            throw new UnprocessableEntityHttpException('Missing data for HistoricPersonAction -> ' . json_encode($object->toArray()));
        }
    }


    /**
     * {@inheritdoc}
     */
    public function toArray(HistoricPersonAction $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
