<?php

namespace App\Service;

use App\Entity\HistoricSms;
use App\Entity\HistoricSmsList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use \PDO;

/**
 * ExtractListService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class HistoricSmsService implements HistoricSmsServiceInterface
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

    public function displayAll($historicSms, $status, $limit) {

        $phones = [];

        if($status == "notSent") {
            $phoneNumbers = $this->em->getRepository('App\Entity\HistoricSmsList')->findBy(['historicSms' => $historicSms, 'dateSended' => null], ['phoneName' => 'asc'], $limit);
        } else {
            $phoneNumbers = $this->em->getRepository('App\Entity\HistoricSmsList')->findBy(['historicSms' => $historicSms]);
        }

        if($phoneNumbers) {
            foreach($phoneNumbers as $phone) {
                $phones[] = $phone->toArray('light');
            }
        }
      
        $historicArray = $this->toArray($historicSms);
        $historicArray['phoneNumbers'] = $phones;

        return $historicArray;

    }


    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {

        $dataArray = is_array($data) ? $data : json_decode($data, true);

        if(isset($dataArray['id'])) {
            if($object = $this->em->getRepository('App\Entity\HistoricSms')->find($dataArray['id'])) {
                unset($dataArray['id']);
                return $this->modify($object, $dataArray);
            }
        }
        unset($dataArray['id']);

        //Submits data
        $object = new HistoricSms();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'historic-sms-create', $dataArray);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'HistoricSms ajoutée',
            'HistoricSms' => $this->toArray($object),
        );
    }

    public function duplicate(HistoricSms $historicSms)
    {
        $dataArray = ['name' => '__copy__'.$historicSms->getName(), 'content' => $historicSms->getContent(), 'signature' => $historicSms->getSignature()];

        //Submits data
        $object = new HistoricSms();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'historic-sms-create', $dataArray);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);


        $HistoricSmsLists = $this->em->getRepository('App\Entity\HistoricSmsList')->findBy(['historicSms' => $historicSms]);

        if($HistoricSmsLists) {
            foreach($HistoricSmsLists as $historicSmsList) {
                    // persist data
                    $newHistoricSmsList = new HistoricSmsList();
                    $newHistoricSmsList->setPhoneName($historicSmsList->getPhoneName());
                    $newHistoricSmsList->setPhoneNumber($historicSmsList->getPhoneNumber());
                    $newHistoricSmsList->setHistoricSms($object);
                    $newHistoricSmsList->setChild($historicSmsList->getChild());
                    $newHistoricSmsList->setPhone($historicSmsList->getPhone());

                    $this->mainService->create($newHistoricSmsList);
                    $this->mainService->persist($newHistoricSmsList);
            }
        }



        return [$object->toArray()];


    }

    /**
     * {@inheritdoc}
     */
    public function delete(HistoricSms $object)
    {
        //Persists data
          $this->em->remove($object);
          $this->em->flush();

        return array(
            'status' => true,
            'message' => 'HistoricSms supprimée',
        );
    }

    /**
     * Returns the list of all families in the array format
     * @return array
     */
    public function findAll($status = null)
    {


        if($status) {
            $lists = $this->em->getRepository('App\Entity\HistoricSms')->findBy(['status' => $status], ['createdAt' => 'desc']);
        } else {
            $lists = $this->em->getRepository('App\Entity\HistoricSms')->findBy([], ['createdAt' => 'desc']);
        }

        $result = [];
        foreach($lists as $list) {
            $result[] = $list->toArray();
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(HistoricSms $object)
    {
        if (
            null === $object->getContent()) {
            throw new UnprocessableEntityHttpException('Missing data for HistoricSms -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(HistoricSms $object, $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'historic-sms-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'HistoricSms modifiée',
            'HistoricSms' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(HistoricSms $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
