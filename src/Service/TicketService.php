<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\Staff;
use App\Entity\Category;
use App\Entity\Location;
use App\Entity\TaskStaff;
use App\Entity\Rdv;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * TicketService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class TicketService implements TicketServiceInterface
{
    private $em;

    private $mainService;
    private $categoryService;
    private $staffService;


    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        CategoryServiceInterface $categoryService,
        StaffServiceInterface $staffService

    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->categoryService = $categoryService;
        $this->staffService = $staffService;

    }

    /**
     * Returns the list of all TICKET
     */
    public function findAll($group = null, $limit = 250)
    {

        $tickets = $this->em
            ->getRepository('App\Entity\Ticket')
            ->findBy(array('suppressed' => 0), array('createdAt' => 'DESC'), $limit);
        ;
        $array = [];

        if($group == null) {
            foreach($tickets as $ticket)
            {
                $array[] = $this->toArray($ticket);
            }
        } else {
            foreach($tickets as $ticket)
            {
                $array[$ticket->getDateCall()->format('Y-m-d')][] = $this->toArray($ticket);
            }
        }
        return $array;

    }

    /**
     * Criteria :
     * persona (string),
     * category_id (int), location_id (int), type (string), origin (string)
     * date_from, date_to (Y-m-d)
     * has_been_treated (bool)
     * recall (bool)
     * limit (default : 250)
     */
    public function findByCriteria(string $datas) {
        $values = json_decode($datas, true);

        if(isset($values['category_id'])) {
              if(!$values['category_id'] != "") {
                $values['category'] = $this->em->getRepository('App\Entity\Category')->find($values['category_id']);
              } else {
                $values['category'] = null;
              }
        } else {
              $values['category'] = null;
        }

        if(isset($values['location_id'])) {
              if($values['location_id'] != "") {
                $values['location'] = $this->em->getRepository('App\Entity\Location')->find($values['location_id']);
              } else {
                $values['location'] = null;
              }
        } else {
              $values['location'] = null;
        }

        if(!isset($values['persona'])) $values['persona'] = null;
        if(!isset($values['type'])) $values['type'] = null;
        if(!isset($values['origin'])) $values['origin'] = null;
        if(!isset($values['date_from'])) $values['date_from'] = null;
        if(!isset($values['date_to'])) $values['date_to'] = null;
        if(!isset($values['has_been_treated'])) $values['has_been_treated'] = null;
        if(!isset($values['recall'])) $values['recall'] = null;


        if(!isset($values['limit'])) $values['limit'] = 250;

        $array = null;
        $tickets = $this->em->getRepository('App\Entity\Ticket')->findByCriteria($values);
        foreach($tickets as $ticket) {
          $array[$ticket->getDateCall()->format('Y-m-d')][] = $this->toArray($ticket);
        }

        if(!$array) return ['message' => 'Aucun ticket trouvé avec ces critères'];

        return $array;

    }

    /**
     * filter_name : persona,
     * category (category_id => category->name),
     * location (location_id => location->name), type, origin (origin_call)
     *
     */
    public function findByFilter($filter_name, $value, $limit = 250)
    {

          if($filter_name == "category") {
            $value = $this->em->getRepository('App\Entity\Category')->findOneBy(['name' => $value]);
          }

          if($filter_name == "location") {
            $value = $this->em->getRepository('App\Entity\Location')->findOneBy(['name' => $value]);
          }

          $tickets = $this->em
              ->getRepository('App\Entity\Ticket')
              ->findBy(array('suppressed' => 0, $filter_name => $value), array('createdAt' => 'DESC'), $limit);
          ;

          $array = [];

          foreach($tickets as $ticket)
          {
              $array[$ticket->getDateCall()->format('Y-m-d')][] = $this->toArray($ticket);
          }

          return $array;
    }

    /**
     * Returns the list of all TICKET need call
     */
    public function findNeedCall($group = null)
    {

        $tickets = $this->em
            ->getRepository('App\Entity\Ticket')
            ->findNeedCall();
        ;

        $array = [];


        if($group == null) {
            foreach($tickets as $ticket)
            {
                $array[] = $this->toArray($ticket);
            }
        } else {
            foreach($tickets as $ticket)
            {
                $array[$ticket->getDateCall()->format('Y-m-d')][] = $this->toArray($ticket);
            }
        }

        return $array;

    }



    /**
     * {@inheritdoc}
     */
    public function setTreated($ticket)
    {
      $ticket->setHasBeenTreated(1);
      $this->mainService->create($ticket);
      $this->mainService->persist($ticket);

      //Returns data
      return array(
          'status' => true,
          'message' => 'Ticket traité',
          'ticket' => $ticket->toArray(),
      );
    }

    public function hydrate($object, $values)
    {

      if(!$staff = $this->em->getRepository('App\Entity\Staff')->find($values['staff_id'])) $staff = null;
      if(!$category  = $this->em->getRepository('App\Entity\Category')->find($values['category_id'])) $category = null;
      if(!$location = $this->em->getRepository('App\Entity\Location')->find($values['location_id'])) $location = null;
      if($values['taskStaff_id']) {
        if(!$taskStaff = $this->em->getRepository('App\Entity\TaskStaff')->find($values['taskStaff_id'])) $taskStaff = null;
      } else {
        $taskStaff = null;
      }
      if($values['rdv_id']) {
        if(!$rdv = $this->em->getRepository('App\Entity\Rdv')->find($values['rdv_id'])) $rdv = null;
      } else {
        $rdv = null;
      }

      (!isset($values['has_been_treated'])) ? $has_been_treated = 0 :  $has_been_treated = $values['has_been_treated'];

      $dateCall = new DateTime($values['date_call']);

      $object->setStaff($staff);
      $object->setName($values['name']);
      $object->setTel($values['tel']);
      $object->setContent($values['content']);
      $object->setCategory($category);
      $object->setLocation($location);
      $object->setRdv($rdv);
      $object->setPersona($values['persona']);
      $object->setTaskStaff($taskStaff);
      $object->setRecall($values['recall']);
      $object->setType($values['type']);
      $object->setDateCall($dateCall);
      $object->setHasBeenTreated($has_been_treated);
      $object->setOriginCall($values['origin_call']);

      return $object;

    }

    public function delete($ticket) {

    (!isset($this->user)) ? $id = 99 : $id = $this->user->getId();

      $ticket->setSuppressed(true);
      $ticket->setSuppressedAt(new DateTime());
      $ticket->setSuppressedBy($id);

      //Persists data
      $this->mainService->persist($ticket);

      return array(
          'status' => true,
          'message' => 'Ticket supprimé',
          'ticket' => $ticket->toArray()
      );
    }

    public function modify(string $data)
    {
        $values = json_decode($data, true);

        $ticket =  $this->em->getRepository('App\Entity\Ticket')->find($values['ticket_id']);

        $ticket = $this->hydrate($ticket, $values);

        $this->mainService->modify($ticket);

        //Persists data
        $this->mainService->persist($ticket);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Ticket modifié',
            'ticket' => $ticket->toArray(),
        );
    }




    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $values = json_decode($data, true);

        //Submits data
        if (is_array($values) && !empty($values)) {

            $object = new Ticket();
            $object = $this->hydrate($object, $values);

            $this->mainService->create($object);
            //Persists data
            $this->mainService->persist($object);
            // update rdv with location information

            if($object->getRdv()) {
                $rdv = $object->getRdv();
                $rdv->setLocation($object->getLocation());
                $this->em->persist($rdv);
                $this->em->flush();
            }

            //Returns data
            return array(
                'status' => true,
                'message' => 'Ticket ajouté',
                'ticket' => $object->toArray(),
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . json_encode($values));

    }


    /**
     * {@inheritdoc}
     */
    public function toArray(Ticket $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray('light'));


        //Gets related product
        if (null !== $object->getCategory()) {
            $objectArray['category'] = $this->categoryService->toArray($object->getCategory());
        }

        //Gets related staff
        if (null !== $object->getStaff()) {
            $objectArray['staff'] = $this->staffService->toArray($object->getStaff());
        }

        //Gets related location
        if (null !== $object->getLocation() && !$object->getLocation()->getSuppressed()) {
            $objectArray['location'] = $this->mainService->toArray($object->getLocation()->toArray());
        }


        return $objectArray;
    }
}
