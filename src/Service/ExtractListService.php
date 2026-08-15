<?php

namespace App\Service;

use App\Entity\ExtractList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use \PDO;

/**
 * ExtractListService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class ExtractListService implements ExtractListServiceInterface
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
        $values = json_decode($data, true);
        $sqlString = trim(str_replace('\n', '', $values['sqlString']));


        $elements = [
            "selectRequest"   => $values['selectRequest'],
            "fromRequest"     => $values['fromRequest'],
            "joinRequest"     => $values['joinRequest'],
            "whereRequest"    => $values['whereRequest'],
            "keyFilter"       => $values['keyFilter'],
            "destinationType" => $values['destinationType']
        ];

        //Submits data

        if( !$object = $this->em->getRepository('App\Entity\ExtractList')->find($values['listId']) ) $object = new ExtractList();

        
        $object->setTitle($values['listName']);
        $object->setContent($sqlString);
        $object->setElements(serialize($elements));
      
        $this->mainService->create($object);
       

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Liste ajoutée',
            'ExtractList' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ExtractList $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Liste supprimée',
        );
    }

    /**
     * Returns the list of all families in the array format
     * @return array
     */
    public function findAll()
    {


        $lists = $this->em->getRepository('App\Entity\ExtractList')->findAll();
        $result = [];
        foreach($lists as $list) {
            $result[] = $list->toArray();
        }
        return $result;
    }


    /**
     *  Returns the result of executed request (mysq)
     *  @return array
     */
    public function listExecuteContent($extractList, $type = "sms") {

        $query = $extractList->getContent();

        $conn = $this->em->getConnection();
        $r = $conn->prepare($query);
        $datas = $r->executeQuery()->fetchAllAssociative();

        $arr = [] ;

        if($type != "sms") return $datas;

        foreach($datas as $k => $data) {
            (isset($data['phone_id'])) ? $phoneId = $data['phone_id'] : $phoneId = null;

            if((isset($data['child_id']))) {
                $currentPhone = "";
                if(!isset($data['phone_number'])) {
                    if(isset($data['phone'])) {
                        $currentPhone = $data['phone'];
                    }
                } else {
                    $currentPhone = $data['phone_number'];
                }
                $phones[$data['child_id']][] = ['phone' => $currentPhone, 'name' => $data['phone_name'], 'phoneId' => $phoneId];
            } elseif (isset($data['number'])) {
                $phones[$data['keyFilter']][] = ['phone' => $data['number'], 'name' => "twilio", 'phoneId' => date('mdHis').$k];
            } else {
                $phones[$data['child_id']][] = [];
                $childId = null;
            }

            if(isset($date['lastname'])) {
                $name = trim($data['lastname'].' '.$data['firstname']);
            } elseif (isset($data['from_person']) && $data['from_person'] != "") {
                $name = $data['from_person'];
            } else {
                $name = "";
            }


        }
        foreach($datas as $k => $data) {

            if(isset($data['child_id'])) {
                $cur_phones = $phones[$data['child_id']];
                $name = trim($data['lastname'].' '.$data['firstname']);
                $idref = $data['child_id'];
            } else {
                $cur_phones = $phones[$data['keyFilter']];
                ($data['from_person'] != "") ? $name = $data['from_person'] : $name = "_no_name".$k;
                $idref = $data['keyFilter'];
            }

            $arr[$name][] = [
                                                    'childId'         => $idref,
                                                    'fullnameReverse' => $name,
                                                    'registrationId'  => "",
                                                    'updatedAt'       => "",
                                                    'status'          => "",
                                                    'sessions'        => "",
                                                    'phones'          => $cur_phones,
                                                    'personal'        => ""
            ];
        }
        ksort($arr);
        return $arr;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(ExtractList $object)
    {
        if (null === $object->getTitle() ||
            null === $object->getContent()) {
            throw new UnprocessableEntityHttpException('Missing data for ExtractList -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(ExtractList $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'extact-list-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Liste modifiée',
            'ExtractList' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(ExtractList $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
