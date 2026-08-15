<?php

namespace App\Service;

use App\Entity\CallTwilio;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * CallTwilioService class
 * @author Sandy
 */
class CallTwilioService implements CallTwilioServiceInterface
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

    public function retrieveAllCalls($from = null, $to = null) {

        // if from or to is null, set it to 3à days before today
        if ($from == null) {
            $from = date('Y-m-d', strtotime('-30 days'));
            $to = date('Y-m-d');
        }

        $conn = $this->em->getConnection();
        $sql = "SELECT * FROM call_twilio WHERE call_date BETWEEN :from AND :to ORDER BY call_date DESC";
        $stmt = $conn->prepare($sql);
        $calls = $stmt->executeQuery([':from' => $from, ':to' => $to])->fetchAllAssociative();

        return $calls;
    }

    public function create($json_data) {

        $data = json_decode($json_data, true);

        $conn = $this->em->getConnection();


        $sql = "SELECT COUNT(*) FROM call_twilio WHERE call_sid = :call_sid";
        $stmt = $conn->prepare($sql);
        $exists = $stmt->executeQuery([':call_sid' => $data['call_sid']])->fetchOne();

        if ($exists == 0) {

            $clean_number = preg_replace('/^(\+33|33)/', '', $data['number']);

            $clean_number = "%".$clean_number."%";
            $clean_number = str_replace(' ', '', $clean_number);

            $sql = 'SELECT pe.firstname, pe.lastname, pe.person_id 
                    FROM person as pe
                    LEFT JOIN person_phone_link as lk on lk.person_id = pe.person_id
                    LEFT JOIN phone as ph on ph.phone_id = lk.phone_id
                    WHERE ph.phone like :phone';

            $stmt = $conn->prepare($sql);
            $person = $stmt->executeQuery([':phone' => $clean_number])->fetchAssociative();

            $from_person = null;
            $person_id = null;

            if ($person) {
                $from_person = $person['firstname'] . ' ' . $person['lastname'];
                $person_id = $person['person_id'];
            }

            $sql = "INSERT INTO call_twilio (call_sid, number, call_time, call_date, status, direction, duration, from_person, person_id) 
                    VALUES (:call_sid, :number, :time, :date, :status, :direction, :duration, :from_person, :person_id)";
            $stmt = $conn->prepare($sql);

            $stmt->execute([
                ':call_sid' => $data['call_sid'],
                ':number' => $data['number'],
                ':time' => $data['time'],
                ':date' => $data['date'],
                ':status' => $data['status'],
                ':direction' => $data['direction'],
                ':duration' => $data['duration'],
                ':from_person' => $from_person,
                ':person_id' => $person_id
            ]);
            $message = "CallTwilio created";
        } else {
            $message = "CallTwilio already exists";
        }

        return ['message' => $message];

    }

    /**
     * {@inheritdoc}
     */
    public function toArray(CallTwilio $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());
        return $objectArray;
    }
}
