<?php

namespace App\Service;

use App\Entity\Reminder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use DateTime;


/**
 * ReminderService class
 * @author Sandy Razafitrimo
 */
class ReminderService implements ReminderServiceInterface
{
    private $em;

    private $mainService;

    private $statusArray = ['awaiting', 'todo', 'inprogress', 'done'];

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
    public function create($dataSend)
    {

        $data = is_array($dataSend) ? $dataSend : json_decode($dataSend, true);

        (isset($data['url'])) ? $url = $data['url'] : $url = null;
        (isset($data['status'])) ? $status = $data['status'] : $status = "awaiting"; // awaiting, todo, inprogress, done
        $vehicle = $this->em->getRepository('App\Entity\Vehicle')->find($data['vehicle']);


        $currentDate = new DateTime();

        $reminder = new Reminder();
        $reminder->setName($data['name']);
        $reminder->setDescription($data['description']);
        $reminder->setUrl($url);
        $reminder->setCriteria($data['criteria']);
        $reminder->setCriteriaValue($data['criteriaValue']);
        $reminder->setCriteriaComparison($data['criteriaComparison']);
        $reminder->setVehicle($vehicle);
        $reminder->setStatus($status);
        $reminder->setDateReminder($currentDate);

        $this->mainService->create($reminder);
        $this->mainService->persist($reminder);


        //Returns data
        return array(
            'status' => true,
            'message' => 'reminder créée',
            'notification' => $this->toArray($reminder),
        );
    }

    public function delete(Reminder $notification) {

    }
   
    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Reminder $object)
    {
       return true;
    }

    /**
     * {@inheritdoc}
     */
    public function list($status = "all", $vehicle_id = null, $type = "array") {


        if($vehicle_id) {
            $vehicle = $this->em->getRepository('App\Entity\Vehicle')->find($vehicle_id);
        } else {
            $vehicle = null;
        }

        $reminders = $this->em->getRepository('App\Entity\Reminder')->findByStatusVehicle($status, $vehicle);

        if($type != "array") return $reminders;

        $remindersArray = [];
        foreach($reminders as $reminder) {
            $remindersArray[] = $reminder->toArray();
        }

        //Returns data
        return $remindersArray;
    }

    /**
     * {@inheritdoc}
     */
    public function modify($reminderId, string $data)
    {
        $reminder = $this->em->getRepository('App\Entity\Reminder')->find($reminderId);

        $data = is_array($dataSend) ? $dataSend : json_decode($dataSend, true);

        if(isset($data['name'])) $reminder->setName($data['name']);
        if(isset($data['description'])) $reminder->setDescription($data['description']);
        if(isset($data['url'])) $reminder->setUrl($data['url']);
        if(isset($data['criteria'])) $reminder->setCriteria($data['criteria']);
        if(isset($data['criteriaValue'])) $reminder->setCriteriaValue($data['criteriaValue']);
        if(isset($data['criteriaComparison'])) $reminder->setCriteriaComparison($data['criteriaComparison']);
        if(isset($data['vehicle'])) $reminder->setVehicle($this->em->getRepository('App\Entity\Vehicle')->find($data['vehicle']));
        if(isset($data['status'])) $reminder->setStatus($status);

        //Persists data
        $this->mainService->modify($reminder);
        $this->mainService->persist($reminder);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Reminder modifié',
            'reminder' => $this->toArray($reminder),
        );
    }

    public function nextStatus($reminderId) {
        $reminder = $this->em->getRepository('App\Entity\Reminder')->find($reminderId);

        $status = $reminder->getStatus();

        $statusKey = array_keys($this->statusArray, $reminder->getStatus())[0];
        $statusKey++;
        if($statusKey == 4) $statusKey = 0;

        $reminder->setStatus($this->statusArray[$statusKey]);

         //Persists data
         $this->mainService->modify($reminder);
         $this->mainService->persist($reminder);
 
         //Returns data
         return array(
             'status' => $reminder->getStatus(),
             'message' => 'Reminder modifié',
             'reminder' => $this->toArray($reminder),
         );

    }


    /**
     * {@inheritdoc}
     */
    public function toArray(Reminder $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

      

        return $objectArray;
    }
}
