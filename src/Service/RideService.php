<?php

namespace App\Service;

use App\Entity\Person;
use App\Entity\Ride;
use App\Entity\StaffPresence;
use DateTime;
use App\Service\PickupService;
use App\Entity\GroupActivity;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\ChildService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * RideService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RideService implements RideServiceInterface
{
    private $em;

    private $mainService;

    private $staffService;

    private $vehicleService;

    private $childService;

    private $mealService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        StaffServiceInterface $staffService,
        VehicleServiceInterface $vehicleService,
        ChildService $childService,
        MealService $mealService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->staffService = $staffService;
        $this->vehicleService = $vehicleService;
        $this->childService = $childService;
        $this->mealService = $mealService;
    }

    /**
     * Adds specific data that could not be added via generic method
     */
    public function addSpecificData(Ride $object, array $data)
    {
        //Should be done from RideType but it returns null...
        if (array_key_exists('start', $data)) {
            $object->setStart(DateTime::createFromFormat('H:i:s', $data['start']));
        }
        if (array_key_exists('arrival', $data)) {
            $object->setArrival(DateTime::createFromFormat('H:i:s', $data['arrival']));
        }

        //Converts to boolean
        if (array_key_exists('locked', $data)) {
            $object->setLocked((bool) $data['locked']);
        }
    }

    public function duplicateRecursive($source, $target) {

        // A retrieve all rides from source
        if(!$source_rides = $this->findAllByDate($source)) return ['message' => 'source_no_ride'];

        // B retrieve all pickups on target
        $pickup_targets['dropin']  = $this->em->getRepository('App\Entity\Pickup')->findAllUnaffected($target, 'dropin');
        $pickup_targets['dropoff'] = $this->em->getRepository('App\Entity\Pickup')->findAllUnaffected($target, 'dropoff');

        // C create table of pickups
        foreach($pickup_targets['dropin'] as $pickup) {
            $p_targets['dropin'][$pickup->getChild()->getChildId()] = $pickup;
        }
        foreach($pickup_targets['dropoff'] as $pickup) {
            $p_targets['dropoff'][$pickup->getChild()->getChildId()] = $pickup;
        }

        // if there is no pickup find return message
        if(!$pickup_targets['dropin'] && !$pickup_targets['dropoff']) return ['message' => 'target_no_pickup'];

        // D List all driver present in target day
        $presenceStaffs =  $this->em->getRepository('App\Entity\StaffPresence')->findStaffsByPresenceDate($target);
        foreach($presenceStaffs as $presenceStaff) {
          $staffArray[$presenceStaff->getStaff()->getStaffId()] = $presenceStaff->getStaff();
          $debug[$presenceStaff->getStaff()->getStaffId()] = $presenceStaff->getStaff()->getPerson()->getFirstname().' '.$presenceStaff->getStaff()->getPerson()->getLastname();

        }


        // E duplicate rides on the new days if not exist
        foreach($source_rides as $s_ride) {

            // create target date object
            $target_date = new DateTime($target);

            // check if staff in source is present in target
            if( ($s_ride->getStaff()) && key_exists($s_ride->getStaff()->getStaffId(), $staffArray)) {
              $staff = $s_ride->getStaff();
              $person = $s_ride->getStaff()->getPerson();
              $staff_name = $person->getFirstname().' '.$person->getLastname();
            } else {
              $staff = null;
              ($s_ride->getStaff()) ? $person = $s_ride->getStaff()->getPerson() : $person = null;
              $staff_name = "driver absent";
              if($person) {
                  $message['target_staff_absent'][] = $person->getFirstname().' '.$person->getLastname();
              } else {
                $message['target_staff_absent'][] = "aucun staff trouvé sur le ride ".$s_ride->getRideId();
              }
            }


            if($s_ride->getVehicle()) {
                $vehicle_name = $s_ride->getVehicle()->getName();
            } else {
              $vehicle_name = "véhicule inconnu";
            }

            if(!$t_ride = $this->em->getRepository('App\Entity\Ride')->findOneBy([
                                                                        'name' => $s_ride->getName(),
                                                                        'kind' => $s_ride->getKind(),
                                                                        'date' => $target_date,
                                                                        'kind' => $s_ride->getKind(),
                                                                      //  'start' => $s_ride->getStart(),
                                                                        'startPoint' => $s_ride->getStartPoint(),
                                                                        'vehicle' => $s_ride->getVehicle()
                                                                        ])) {
                $t_ride = new Ride();
                $message['target_ride_created'][$s_ride->getKind()][] = $s_ride->getName().' - '.$staff_name.' - '.$vehicle_name.' - Départ: '.$s_ride->getStart()->format('H:i').', '.$s_ride->getStartPoint();
            } else {
              $message['target_ride_updated'][$s_ride->getKind()][] = $s_ride->getName().' - '.$staff_name.' - '.$vehicle_name.' - Départ: '.$s_ride->getStart()->format('H:i').', '.$s_ride->getStartPoint();

            }
            $t_ride->setLocked(0);
            $t_ride->setDate($target_date);
            $t_ride->setKind($s_ride->getKind());
            $t_ride->setName($s_ride->getName());
            $t_ride->setPlaces($s_ride->getPlaces());
            $t_ride->setStart($s_ride->getStart());
            $t_ride->setArrival($s_ride->getArrival());
            $t_ride->setStartPoint($s_ride->getStartPoint());
            $t_ride->setEndPoint($s_ride->getEndPoint());
            $t_ride->setVehicle($s_ride->getVehicle());
            $t_ride->setStaff($staff);
            $userId = 99;
            $t_ride->setCreatedAt(new DateTime());
            $t_ride->setCreatedBy($userId);
            $t_ride->setUpdatedAt(new DateTime());
            $t_ride->setUpdatedBy($userId);
            $t_ride->setSuppressed(0);

            // debug line, delete after debug
            $debugId = $t_ride->getName().' '.$staff_name;
            $debug[$debugId] = [];


            // list all pickup in source if exist child exist in target > affect in ride
            foreach($s_ride->getPickups() as $o_pickup) {

              $kindname = $t_ride->getKind();

              if(\key_exists($o_pickup->getChild()->getChildId(), $p_targets[$kindname])) {
                // new pickup
                $t_pickup = $p_targets[$kindname][$o_pickup->getChild()->getChildId()];

                // check if same moment

                $is_valid = 0;
                
                $t_time = intval($t_pickup->getStart()->format('Hi'));
                $o_time = intval($o_pickup->getStart()->format('Hi'));

                if( $kindname == "dropin") {
                    if($t_time < 1200) $t_moment = "AM";
                    if($t_time > 1200) $t_moment = "PM";

                    if($o_time < 1200) $o_moment = "AM";
                    if($o_time > 1200) $o_moment = "PM";
                } else {
                    if($t_time < 1300) $t_moment = "AM";
                    if($t_time > 1500) $t_moment = "PM";

                    if($o_time < 1300) $o_moment = "AM";
                    if($o_time > 1500) $o_moment = "PM";

                }

                if($t_moment == $o_moment || $o_time == "0000") $is_valid = 1;


                if($is_valid && $t_pickup->getRegistration() != null && $o_pickup->getRegistration() != null) { 
                    
                    if($t_pickup->getRegistration()->getProduct() != $o_pickup->getRegistration()->getProduct()) {
                        $message['target_child_exists_but_different_product'][] =  $o_pickup->getChild()->getLastname().' '.$o_pickup->getChild()->getFirstname();
                    }
                    $t_pickup->setSortOrder($o_pickup->getSortOrder());
                    $start_time_string = $target.' '.$o_pickup->getStart()->format('H:i:s');

                    if($o_pickup->getAddress() != $t_pickup->getAddress()) {
                        $message['address_changed'][] =  $o_pickup->getChild()->getLastname().' '.$o_pickup->getChild()->getFirstname();
                    }


                    $t_pickup->setStart(new DateTime($start_time_string));
                    $t_pickup->setPhone($o_pickup->getPhone());
                    $t_pickup->setPostal($o_pickup->getPostal());
                    $t_pickup->setAddress($o_pickup->getAddress());
                    $t_pickup->setLatitude($o_pickup->getLatitude());
                    $t_pickup->setLongitude($o_pickup->getLongitude());
                    $t_ride->addPickup($t_pickup);
    
                    $message['target_child_associated_to_ride'][$o_pickup->getChild()->getChildId()] = $o_pickup->getChild()->getLastname().' '.$o_pickup->getChild()->getFirstname();
    
                    // debug line, delete after debug
                    $debug[$debugId][] = $t_pickup->toArray();

                   
                } else {
                    $message['no_start_time_for'][] =  $o_pickup->getChild()->getLastname().' '.$o_pickup->getChild()->getFirstname();
                }
              

              } else  {
                $message['target_child_not_in_target'][$o_pickup->getChild()->getChildId()] = $o_pickup->getChild()->getLastname().' '.$o_pickup->getChild()->getFirstname();
              }
            }
            $this->em->persist($t_ride);
            $this->em->flush();

        }

        if(isset($message['target_child_associated_to_ride'])) asort($message['target_child_associated_to_ride']);
        if(isset($message['target_child_not_in_target'])) asort($message['target_child_not_in_target']);



        return [$message];
    }

    public function retrieveGroupActivity($staff, $date, $kind) {
        
        if(!$rides = $this->em->getRepository('App\Entity\Ride')->findOneRideByDateStaffKind($date, $staff, $kind)) {
            return ['status' => 'no rides founded'];
        }

        // list of childs
        foreach($rides as $ride) {
            foreach($ride->getPickups() as $pickup) {
                $childLists[] = $pickup->getChild();
            }
        }

        $datas = [];
        foreach($childLists as $child) {
            $gpe = $this->em->getRepository('App\Entity\GroupActivity')->findOneGroupByDateChild($child, $date, $kind);
            if(!$gpe) continue; 
            foreach($gpe->getStaff() as $link) {
                $staffArray[] = $link->getStaff()->getPerson()->getFirstname().' '.$link->getStaff()->getPerson()->getLastname();
            }

            $datas[$child->getChildId()] = [
                                                'child_name' => $child->getFirstname().' '.$child->getLastname(),
                                                'staff_name' => implode(', ' , $staffArray),
                                                'time_start' => $gpe->getStart()->format('H:i'),
                                                'sport'      => $gpe->getSport()->getName()

                                            ] ;
                        
            unset($staffArray);
        }

        return $datas;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new Ride();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'ride-create', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Trajet ajouté',
            'ride' => $this->toArray($object),
        );
    }


    /**
     * {@inheritdoc}
     */
    public function createMultiple(string $data)
    {
        $data = json_decode($data, true);

        $arr = [];

        if (is_array($data) && !empty($data)) {
            foreach ($data as $rideData) {
                //Submits data
                $object = new Ride();
                $this->mainService->create($object);
                $this->mainService->submit($object, 'ride-create', $rideData);
                $this->addSpecificData($object, $rideData);

                //Checks if entity has been filled
                $this->isEntityFilled($object);

                //Persists data
                $this->mainService->persist($object);

            
                if($rideData['kind'] == "dropoff" && isset($arr['dropoff'][$rideData['staff']]) ){
                    if( $arr['dropoff'][$rideData['staff']]['start'] == "11:30:00" ) {
                        $arr[$rideData['kind']][$rideData['staff']] = ['start' => $rideData['start'], 'rideId' => $object->getRideId(), 'ride' => $object];           
                    }
                } else {
                    if($rideData['kind'] == "dropin" && isset($arr['dropin'][$rideData['staff']])) continue;
                    $arr[$rideData['kind']][$rideData['staff']] = ['start' => $rideData['start'], 'rideId' => $object->getRideId(), 'ride' => $object];           
                }


            }

            return $arr;
            if(isset($arr['dropin'])) {
                // foreach dropin
               foreach($arr['dropin'] as $staffId => $elementIn) {
                   // if in dropoff existe key  
                    if(isset($arr['dropoff'][$staffId])) {
                        $elementOff = $arr['dropoff'][$staffId];
                        $rideIn = $elementIn['ride'];

                        $rideIn->setLinkedRide($elementOff['ride']);

                        $this->em->persist($rideIn);
                        $this->em->flush();
            
                    }
               }
                
               
            }

            //Returns data
            return array(
                'status' => true,
                'message' => 'Trajets ajoutés',
                'arr' => $arr
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . json_encode($data));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Ride $object)
    {
        //Removes ride from pickups
        foreach ($object->getPickups() as $pickup) {
            $pickup
                ->setRide(null)
                ->setSortOrder(null)
            ;
            $this->mainService->modify($pickup);
            $this->em->persist($pickup);
        }

        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Trajet supprimé',
        );
    }

    /**
     * Returns the list of all rides by date
     * @return array
     */
    public function findAllByDate(string $date)
    {
        return $this->em
            ->getRepository('App\Entity\Ride')
            ->findAllByDate($date)
        ;
    }

    public function findRideTvList($date, $from, $to) {
        $rides = $this->em->getRepository('App\Entity\Ride')->findAllByDateAndBetween($date, $from, $to);
        $arr = [];
        foreach($rides as $ride) {

            $pus = []; 
            foreach($ride->getPickups() as $pu) {
                if($pu->getStatus() != "npec") {
                    $pus[] = [
                        'child' => [
                                        'firstname' => $pu->getChild()->getFirstname(),
                                        'lastname' => $pu->getChild()->getLastname(),
                                        'photo'    => $pu->getChild()->getPhoto(),
                                        'age'      => $pu->getChild()->getAge(),
                                        'start_time' => $pu->getStart()->format('H:i'),
                                        'order' => $pu->getSortOrder()
                        ]
                    ];
                }
            }

            usort($pus, function ($a, $b) {
                return strtotime($a['child']['start_time']) - strtotime($b['child']['start_time']);
            });

            if($ride->getStaff()) {
                                    $staff = [
                                        'name' => $ride->getStaff()->getPerson()->getFirstname(),
                                        'photo' => $ride->getStaff()->getPerson()->getPhoto()
                                    ];
            } else {
                $staff = null;
            }

            $myRide = [
                        'name'    => $ride->getName(),
                        'kind'    => $ride->getKind(),
                        'start'   => $ride->getStart()->format('H:i'),
                        'pickups' => $pus,
                        'staff'   => $staff,
                        'start'   => $ride->getStart()->format('H:i')  
            ];

            $arr[] = $myRide;

        }
        return $arr;
    }

    /**
     * Returns the list of all rides by date and params
     * @return array
     */
    public function findRealtime($date, $kind, $moment)
    {
      return $this->em
          ->getRepository('App\Entity\Ride')
          ->findAllByDateAndParams($date, $kind, $moment)
      ;
    }

    public function updateReport(string $data) {
        $data = json_decode($data, true);

        // retrive ride
        $ride = $this->em->getRepository('App\Entity\Ride')->find($data['rideId']);

        // update report
        foreach($data['report'] as $report) {
            $el =explode('|', $report);
            $reportArray[] = [ $el[0] => $el[1] ];
        }

        $ride->setReport($reportArray);

        $this->em->persist($ride);
        $this->em->flush();

        return $ride->toArray();

    }


    /**
     * Returns the list of all rides by date and kind
     * @return array
     */
    public function findAllByDateAndKind(string $date, string $kind)
    {
        return $this->em
            ->getRepository('App\Entity\Ride')
            ->findAllByDateAndKind($date, $kind)
        ;
    }

    /**
     * Returns all the rides by status
     * @return array
     */
    public function findAllByStatus(string $status)
    {
        return $this->em
            ->getRepository('App\Entity\Ride')
            ->findAllByStatus($status)
        ;
    }

    /**
     * Returns the rides linked to date and person
     * @return array
     */
    public function findAllByDateByPersonId(string $date, Person $person)
    {
        return $this->em
            ->getRepository('App\Entity\Ride')
            ->findAllByDateByPersonId($date, $person)
        ;
    }

    /**
     * Returns the rides linked to date and staff
     * @return array
     */
    public function findAllByDateByStaff(string $date, $staff)
    {
        return $this->em
            ->getRepository('App\Entity\Ride')
            ->findAllByDateByStaff($date, $staff)
        ;
    }

    /**
     * Returns the rides that are linked to another one for date
     * @return array
     */
    public function findAllLinked(string $date)
    {
        return $this->em
            ->getRepository('App\Entity\Ride')
            ->findAllLinked($date)
        ;
    }

    /**
     * Returns the ride correspoonding to rideId
     * @return array
     */
    public function findOneById(int $rideId)
    {
        return $this->em
            ->getRepository('App\Entity\Ride')
            ->findOneById($rideId)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Ride $object)
    {
        if (null === $object->getDate() ||
            null === $object->getName()) {
            throw new UnprocessableEntityHttpException('Missing data for Ride -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Ride $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'ride-modify', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Trajet modifié',
            'ride' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Ride $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related staff
        if (null !== $object->getStaff() && !$object->getStaff()->getSuppressed()) {
            $objectArray['staff'] = $this->staffService->toArray($object->getStaff());
        }

        //Gets related vehicle
        if (null !== $object->getVehicle() && !$object->getVehicle()->getSuppressed()) {
            $objectArray['vehicle'] = $this->mainService->toArray($object->getVehicle()->toArray());
        }

        //Gets related LinkedRide
        if (null !== $object->getLinkedRide() && !$object->getLinkedRide()->getSuppressed()) {
            $objectArray['linkedRide'] = $this->mainService->toArray($object->getLinkedRide()->toArray());
        }

        //Gets related pickups
        if (null !== $object->getPickups()) {
            $pickups = array();
            $i = 0;
            foreach($object->getPickups() as $pickup) {
                if (!$pickup->getSuppressed()) {
                    $pickups[$i] = $this->mainService->toArray($pickup->toArray());

                    if(is_object($pickup->getChild())) {
                        $pickups[$i]['child'] = $this->childService->toArray($pickup->getChild());
                    }

                    // latest meal
                    $meal = $this->mealService->latestMealByChild($pickup->getChild()->getChildId());
                    if($meal) {
                        $mealArray = $this->mealService->toArray($meal);
                    } else {
                        $mealArray = null;
                    }
                    $pickups[$i]['child']['latestMeal'] = $mealArray;


                    if($registration = $pickup->getRegistration()){
                        if($product = $registration->getProduct()) {
                                    $productName = $product->getNameFr();
                                    $productIsOffered = $product->getIsOffered();
                                    $hasLunch    = $product->getLunch();
                                
                                    if (null !== $product->getHours()) {
                                        $hours = array();
                                        $j = 0;
                                        foreach ($product->getHours() as $hour) {
                                            if (null !== $hour->getStart()) {
                                                $hours[$j]['start'] = $hour->getStart()->format('H:i');
                                            }
                                            if (null !== $hour->getEnd()) {
                                                $hours[$j]['end'] = $hour->getEnd()->format('H:i');
                                            }
                                            if (null !== $hour->getIsFull()) {
                                                $hours[$j]['is_full'] = $hour->getIsFull();
                                            }
                                            if (null !== $hour->getMessageFr()) {
                                                $hours[$j]['message_fr'] = $hour->getMessageFr();
                                            }
                                            if (null !== $hour->getMessageEn()) {
                                                $hours[$j]['message_en'] = $hour->getMessageEn();
                                            }
                                            ++$j;
                                        }
                                    }

                                    $productHours = $hours;
                        
                        }
                        $registrationData = [
                                                "registrationId" => $registration->getRegistrationId(),
                                                "hasLunch"       => $hasLunch,
                                                "productHours"   => $productHours,
                                                "productName"    => $productName,
                                                'productIsOffered' => $productIsOffered
                                            ];
                        $pickups[$i]['registrationData'] = $registrationData;
                        
                    }

                    //latest PEC
                    $latestPickup = $this->em
                            ->getRepository('App\Entity\Pickup')->findOneBy(
                                                                                        array('child' => $pickup->getChild(), 'kind' => $pickup->getKind(), 'status' => 'pec'),
                                                                                        array('start' => 'desc'),
                                                                                        1
                                                                                    );

                    if(!$latestPickup)
                    {
                        $latestPEC = null;
                    } else {
                        $latestPEC = [
                                                'kind' => $latestPickup->getKind(),
                                                'status_change' => $latestPickup->getStatusChange(),
                                                'start' => $latestPickup->getStart(),
                                                'status' => $latestPickup->getStatus()
                                    ];
                    }

                    $pickups[$i]['child']['latestPEC'] = $latestPEC;

                    $i++;
                }
            }
            $objectArray['pickups'] = $pickups;
        }

        return $objectArray;
    }
}
