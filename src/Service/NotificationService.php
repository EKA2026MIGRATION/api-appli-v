<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\NotificationPersonLink;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use DateTime;


/**
 * NotificationService class
 * @author Sandy Razafitrimo
 */
class NotificationService implements NotificationServiceInterface
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
    public function create($dataSend)
    {

        $data = is_array($dataSend) ? $dataSend : json_decode($dataSend, true);

        (isset($data['url'])) ? $url = $data['url'] : $url = null;
        $currentDate = new DateTime();

        $notification = new Notification();
        $notification->setName($data['name']);
        $notification->setDescription($data['description']);
        $notification->setUrl($url);
        $notification->setDateNotification($currentDate);
        $this->mainService->create($notification);
        $this->mainService->persist($notification);

        $persons = $this->em->getRepository('App\Entity\Person')->findByUserRole($data['target_role']);

        foreach($persons as $person) {
            $personList[] = $person->getFullName();

            $link = new NotificationPersonLink();
            $link->setPerson($person);
            $link->setNotification($notification);
            $this->mainService->persist($link);

            $notification->addNotificationPersonLink($link);
            $this->mainService->persist($notification);
        }

        //Returns data
        return array(
            'status' => true,
            'message' => 'notification créée',
            'notification' => $this->toArray($notification),
            'person' => $personList
        );
    }

    public function findByPersonId($personId) {

        $person = $this->em->getRepository('App\Entity\Person')->find($personId);
        if(!$notifications = $this->em->getRepository('App\Entity\Notification')->findByPerson($person)) return null;
        
        $results = [];
        foreach($notifications as $notification) {
            $results[] =  $this->toArray($notification);
        }
         //Returns data
         return $results;
    }

    public function delete(Notification $notification) {

    }

    /**
     * {@inheritdoc}
     */
    public function removePerson($notificationId, $personId)
    {

        $notification = $this->em->getRepository('App\Entity\Notification')->find($notificationId);
        $person = $this->em->getRepository('App\Entity\Person')->find($personId);

        //Removes links from notification
        if($notification->getNotificationPersonLinks()) {
            foreach ($notification->getNotificationPersonLinks() as $link) {
                if($person == $link->getPerson()) {
                    $this->em->remove($link);
                    $this->em->flush();
                }
              
            }
        }

        return array(
            'status' => true,
            'message' => 'Notification supprimée',
        );
    }

   
    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Notification $object)
    {
       return true;
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Notification $object, string $data)
    {
       

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Repas modifié',
            'meal' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Notification $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

      

        return $objectArray;
    }
}
