<?php

namespace App\Service;

use App\Entity\Pickup;
use App\Entity\PickupActivity;
use App\Entity\Ride;
use App\Entity\Child;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * PickupService class.
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupService implements PickupServiceInterface
{
    private $em;

    private $staffService;

    private $mainService;

    private $rideService;

    private $childPresenceService;

    public function __construct(
        EntityManagerInterface $em,
        StaffServiceInterface $staffService,
        MainServiceInterface $mainService,
        RideServiceInterface $rideService,
        ChildPresenceServiceInterface $childPresenceService
    ) {
        $this->em = $em;
        $this->staffService = $staffService;
        $this->mainService = $mainService;
        $this->rideService = $rideService;
        $this->childPresenceService = $childPresenceService;
    }

    /**
     * Affects the Pickups to Rides for a specific date.
     */
    public function affect($date, $kind, $force)
    {
        //Unaffects Pickups if force is requested
        if ($force) {
            $this->unaffect($date, $kind);
        }

        //Affects all the Pickups
        if ('all' === $kind) {
            //Affect the dropin Pickups
            $this->affectPickups($date, 'dropin');

            //Affect the dropoff Pickups using LinkedRide
            $this->affectPickupsLinkedRide($date);

            //Affect the dropoff Pickups not linked via LinkedRide
            $this->affectPickups($date, 'dropoff');
        //Affects only the specific kind of Pickup
        } else {
            $this->affectPickups($date, $kind);
        }
    }

    /**
     * Affects the Pickups to Rides.
     */
    public function affectPickups($date, $kind)
    {
        //Defines Pickups to use
        $pickupsSortOrder = $this->countAllUnaffected($date, $kind);
        $pickups = $this->findAllUnaffected($date, $kind);

        if (!empty($pickups)) {
            //Sorts Pickup by greater number of postal code and builds the array([postal][md5(address)]['pickups'][Pickups]) to group same addresses in same Ride
            $pickupsSorted = array();
            foreach ($pickupsSortOrder as $pickupSortOrder) {
                foreach ($pickups as $pickup) {
                    //Checks address with geocoding
                    $this->checkCoordinates($pickup);
                    //Adds Pickup to its corresponding group of postal code
                    if ($pickupSortOrder['postal'] === $pickup->getPostal()) {
                        $md5 = md5($pickup->getLongitude().$pickup->getLatitude().$pickup->getStart()->format('H:i'));

                        $places = 0 === (int) $pickup->getPlaces() ? 1 : (int) $pickup->getPlaces();
                        $places = isset($pickupsSorted[$pickupSortOrder['postal']][$md5]['places']) ? $pickupsSorted[$pickupSortOrder['postal']][$md5]['places'] + $places : $places;
                        $start = isset($pickupsSorted[$pickupSortOrder['postal']][$md5]['start']) ? $pickupsSorted[$pickupSortOrder['postal']][$md5]['start'] : $pickup->getStart();

                        $pickupsSorted[$pickupSortOrder['postal']][$md5]['places'] = $places;
                        $pickupsSorted[$pickupSortOrder['postal']][$md5]['start'] = $start;
                        $pickupsSorted[$pickupSortOrder['postal']][$md5]['pickups'][] = $pickup;
                    }
                }
            }
            unset($pickups);
            unset($pickupsSortOrder);

            //Affects Pickups in the order of postal code and makes as many passes as maxDriverZones
            $rides = $this->rideService->findAllByDateAndKind($date, $kind);
            $maxDriverZones = $this->staffService->getMaxDriverZones() - 1;
            if (null !== $rides) {
                for ($priority = 0; $priority <= $maxDriverZones; ++$priority) {
                    foreach ($pickupsSorted as $postal => $pickupsByPostal) {
                        foreach ($pickupsByPostal as $pickups) {
                            $this->affectRide($rides, $pickups, $postal, $priority);
                        }
                    }
                }
            }
        }
    }

    public function MultipleaffectPickupLinkedRide(string $data) {
        $datas = json_decode($data, true);

        foreach($datas as $data) {
            $ride = $this->em->getRepository('App\Entity\Ride')->find($data['rideIdIn']);
            $result[] = $this->affectPickupLinkedRide($ride, $data['rideIdOff']);
        }

        return $result;
    }


    /**
     * Affects all the Pickups that correspond between Ride and LinkedRide.
     */
    public function affectPickupLinkedRide($ride, $linkedRideId = null)
    {

        if($linkedRideId > 0) {
            $linkedRide = $this->rideService->findOneById($linkedRideId);
        } else {
            $linkedRide = $this->rideService->findOneById($ride->getLinkedRide()->getRideId());
        }
        $linkedRideDateStart = new DateTime($linkedRide->getDate()->format('Y-m-d').' '.$linkedRide->getStart()->format('H:i:s'));
        $linkedRideDateArrival = new DateTime($linkedRide->getDate()->format('Y-m-d').' '.$linkedRide->getArrival()->format('H:i:s'));

        $pickups = $ride->getPickups();
        $orderPickups = [];
        foreach($pickups as $pickup) {
            $orderPickups[$pickup->getSortOrder()] = $pickup;
        }

        ksort($orderPickups);

        krsort($orderPickups);

        $j = 1;

        foreach($orderPickups as $pickup) {
            $newsPickups[$j] = $pickup;
            $j++;
        }

       



        $i = 0;

        foreach ($newsPickups as $order => $pickup) {
            //Affects Pickup to LinkedRide if there is a correponding dropoff for the same child and with is start within the time slot for the Ride
            $pickupDropoff = $this->em->getRepository('App\Entity\Pickup')->findOneByDateKindChild($ride->getDate()->format('Y-m-d'), 'dropoff', $pickup->getChild());
            if (null !== $pickupDropoff &&
                $pickupDropoff->getStart() >= $linkedRideDateStart &&
                $pickupDropoff->getStart() <= $linkedRideDateArrival
            ) {
                $pickupDropoff
                    ->setRide($linkedRide)
                    ->setSortOrder($order)
                    ->setStatus('automatic')
                    ->setStatusChange(new DateTime())
                ;
                $this->mainService->modify($pickupDropoff);
                $this->mainService->persist($pickupDropoff);
                $this->em->refresh($linkedRide);

                $i++;
            }
        }

        return ['rideDropIn' => $ride->getRideId(), 'rideDropOff' => $linkedRide->getRideId(), 'nbPickDuplicate' => $i];
    }

    /**
     * Affects the Pickups (dropoff) to Rides using LinkedRide.
     */
    public function affectPickupsLinkedRide($date)
    {
        $rides = $this->rideService->findAllLinked($date);
        if (null !== $rides) {
            foreach ($rides as $ride) {
                $this->affectPickupLinkedRide($ride);
            }
        }
    }

    /**
     * Affects the Ride to the Pickup.
     */
    public function affectRide($rides, $pickups, $postal, $priority)
    {
        //Filters Rides to keep only those that are NOT full
        $rides = array_filter($rides, function ($i) {
            $ridePlaces = 0 === (int) $i->getPlaces() ? 8 : (int) $i->getPlaces();

            return (int) $ridePlaces > $i->getOccupiedPlaces() && !$i->getLocked();
        });

        //Filters Pickups to keep only those that are NOT affected
        $pickups['pickups'] = array_filter($pickups['pickups'], function ($i) {
            return null === $i->getRide();
        });

        foreach ($rides as $ride) {
            /**
             * Updates Pickup if
             * - Ride is linked to Pickup's postal code using Driver + DriverPriority + DriverZonesPriorities
             * - Ride is not full AND can contain all the Pickups (including places) for the same address
             * - Pickup Start is within Ride's time slot (between start and arrival).
             */
            $rideDateStart = new DateTime($ride->getDate()->format('Y-m-d').' '.$ride->getStart()->format('H:i:s'));
            $rideDateArrival = new DateTime($ride->getDate()->format('Y-m-d').' '.$ride->getArrival()->format('H:i:s'));
            $ridePlaces = 0 === (int) $ride->getPlaces() ? 8 : (int) $ride->getPlaces();

            if (isset($ride->getStaff()->getDriverZones()[$priority]) &&
                null !== $ride->getStaff()->getDriverZones()[$priority] &&
                $postal === (int) $ride->getStaff()->getDriverZones()[$priority]->getPostal() &&
                $pickups['places'] + $ride->getOccupiedPlaces() <= $ridePlaces &&
                $pickups['start'] >= $rideDateStart &&
                $pickups['start'] <= $rideDateArrival
            ) {
                foreach ($pickups['pickups'] as $pickup) {
                    $pickup
                        ->setRide($ride)
                        ->setSortOrder($ride->getPickups()->count() + 1)
                        ->setStatus('automatic')
                        ->setStatusChange(new DateTime())
                    ;
                    $this->mainService->modify($pickup);
                    $this->mainService->persist($pickup);
                    $this->em->refresh($ride);
                }

                break;
            }
        }
    }

    public function updateSmsSentData(string $data)
    {
        $elements = explode(';', $data);

        $pickupId = str_replace('"', '', $elements[0]);
        $number = str_replace('"', '', $elements[1]);

        $pickup = $this->em->getRepository('App\Entity\Pickup')->find($pickupId);
        $timeSent = date('Y-m-d H:i');
        $timeSentFr = date('d/m/Y - H:i');
        $data = ['timeSent' => $timeSent, 'number' => $number];

        ($pickup->getSmsSentData() == null) ? $dataArray = [] : $dataArray = $pickup->getSmsSentData();

        $dataArray[] = $data;

        $pickup->setSmsSentData($dataArray);

        $this->em->persist($pickup);
        $this->em->flush();

        //Returns data
        return array(
              'status' => true,
              'message' => 'sms sent data mis à jour',
              'smsSendData' => $pickup->getSmsSentData(),
              'currentDateSent' => $timeSentFr,
          );
    }

    /**
     * Checks gps coordinates.
     */
    public function checkCoordinates($object, $force = false)
    {
        if ($force ||
            null === $object->getLatitude() ||
            null === $object->getLongitude() ||
            null === $object->getPostal() ||
            5 != strlen($object->getPostal())
        ) {
            $this->mainService->addCoordinates($object);
        }
    }

    /**
     * Gets all the Pickups by date.
     */
    public function countAllUnaffected(string $date, string $kind)
    {
        return $this->em
            ->getRepository('App\Entity\Pickup')
            ->countAllUnaffected($date, $kind)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data, $return = true)
    {
        //Submits data
        $object = new Pickup();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'pickup-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Checks coordinates
        $this->checkCoordinates($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        if ($return) {
            return array(
                'status' => true,
                'message' => 'Pickup ajouté',
                'pickup' => $this->toArray($object),
            );
        }
    }

    /**
     * Creates multiples Pickups.
     */
    public function createMultiple(string $data)
    {
        $data = json_decode($data, true);
        if (is_array($data) && !empty($data)) {
            foreach ($data as $newPickup) {
                $this->create(json_encode($newPickup), false);
            }

            //Returns data
            return array(
                'status' => true,
                'message' => 'Pickups ajoutés',
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> '.json_encode($data));
    }


    public function listByRegistrationId(int $registrationId) {
        $pickups = $this->em->getRepository('App\Entity\Pickup')->findByRegistrationId($registrationId);
        if (!empty($pickups)) {
            foreach ($pickups as $pickup) {
                $arr[] = $this->toArray($pickup);
            }
        }
        return ['pickups' => $arr];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Pickup $object, $return = true)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        if ($return) {
            return array(
                'status' => true,
                'message' => 'Pickup supprimé',
            );
        }
    }

    /**
     * Deletes Pickup by registrationId.
     */
    public function deleteByRegistrationId(int $registrationId)
    {
        $pickups = $this->em->getRepository('App\Entity\Pickup')->findByRegistrationId($registrationId);
        if (!empty($pickups)) {
            foreach ($pickups as $pickup) {
                $this->delete($pickup, false);
            }

            return array(
                'status' => true,
                'message' => 'Pickup supprimés',
            );
        }
    }

    /**
     * Modifies the dispatch for Pickups.
     */
    public function dispatch(string $data)
    {
        //Modifies dispatch and sort order
        $data = json_decode($data, true);
        if (is_array($data) && !empty($data)) {
            foreach ($data as $dispatch) {
                $pickup = $this->em->getRepository('App\Entity\Pickup')->findOneById($dispatch['pickupId']);
                if ($pickup instanceof Pickup && !$pickup->getSuppressed()) {
                    //Modifies Ride
                    $ride = $this->em->getRepository('App\Entity\Ride')->findOneById($dispatch['rideId']);
                    if ($ride instanceof Ride && !$ride->getSuppressed()) {
                        $pickup->setRide($ride);
                    } elseif (null === $dispatch['rideId'] || 'null' === $dispatch['rideId']) {
                        $pickup->setRide(null);
                    }

                    //Modifies Start
                    if (!empty($dispatch['start'] && '' !== $dispatch['start'])) {
                        $start = DateTime::createFromFormat('Y-m-d H:i:s', $dispatch['start']);
                        if ($start instanceof DateTime) {
                            $pickup->setStart($start);
                        }
                    } elseif (null === $dispatch['start'] || 'null' === $dispatch['start']) {
                        $pickup->setStart(null);
                    }

                    //Modifies other fields
                    $pickup
                        ->setSortOrder($dispatch['sortOrder'])
                        ->setValidated($dispatch['validated'])
                    ;

                    //Persists data
                    $this->mainService->modify($pickup);
                    $this->mainService->persist($pickup);
                }
            }

            //Returns data
            return array(
                'status' => true,
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> '.json_encode($data));
    }

    /**
     * Gets all the Pickups by date.
     */
    public function findAllByDate(string $date, string $kind)
    {
        return $this->em
            ->getRepository('App\Entity\Pickup')
            ->findAllByDate($date, $kind)
        ;
    }

    
    public function listByWeek($monday) {


        $groupTimes = [
                            0 => '00h - 8h',
                            1 => '08h - 10h',
                            2 => '10h - 12h',
                            3 => '12h - 14h',
                            4 => '14h - 16h',
                            5 => '16h - 18h',
                            6 => '18h - 24h'
        ];


        $currentDate = new DateTime($monday);
    

        for($i = 0; $i < 7; $i++) {

            foreach(['dropin', 'dropoff'] as $kind) {
                $pickups = $this->findAllByDate($currentDate->format('Y-m-d'), $kind);
                if($pickups) {
                    foreach($pickups as $pickup) {
                        $child = $pickup->getChild();
                        if($ride = $pickup->getRide()) {
                            $rideInfo = $ride->getName().' '.$ride->getStart()->format('H:i');
                            ($ride->getStaff()) ? $driver = $ride->getStaff()->getPerson()->getFirstname() : $driver = null; 
                        } else {
                            $rideInfo = null;
                            $driver   = null;
                        }

                        $start = $pickup->getStart()->format('G');

                        if($start < 8) $key = 0;
                        if($start >= 8  && $start < 10) $key = 1;
                        if($start >= 10 && $start < 12) $key = 2;
                        if($start >= 12 && $start < 14) $key = 3;
                        if($start >= 14 && $start < 16) $key = 4;
                        if($start >= 16 && $start < 18) $key = 5;
                        if($start >= 18) $key = 6;


                        $all[$kind][$groupTimes[$key]][$pickup->getChild()->getLastname().$pickup->getPickupId()] = [
                                                                            'pickupId'    => $pickup->getPickupId(),
                                                                            'childId'     => $child->getChildId(),
                                                                            'firstname'   => $child->getFirstname(),
                                                                            'lastname'    => $child->getLastname(),
                                                                            'status'      => $pickup->getStatus(),
                                                                            'address'     => $pickup->getAddress(),
                                                                            'start'       => $pickup->getStart()->format('H:i'),
                                                                            'payment_due' => $pickup->getPaymentDue(),
                                                                            'payment_done'=> $pickup->getPaymentDone(),
                                                                            'ride_data'   => $rideInfo,
                                                                            'driver'      => $driver,
                                                                            'info_medical'=> $child->getMedical()

                        ];
                    }
                } else {
                    $all[$kind] = [];
                }
                if(count($all[$kind]) > 0) {
                    foreach($all[$kind] as $groupTime => $element) {
                        ksort($element);
                        $all[$kind][$groupTime] = $element;
                    }
                    ksort($all[$kind]);
                }
               
                
            }
            
            $result[$currentDate->format('Y-m-d')] = $all;

            $currentDate = $currentDate->modify('+1 day');

            unset($all);
        }


        return $result;
    }

    /**
     * Gets all the Pickups by status.
     */
    public function findAllByStatus(string $date, string $status)
    {
        return $this->em
            ->getRepository('App\Entity\Pickup')
            ->findAllByStatus($date, $status)
        ;
    }

    /**
     *Gets all the Pickups unaffected.
     */
    public function findAllUnaffected(string $date, string $kind)
    {
        return $this->em
            ->getRepository('App\Entity\Pickup')
            ->findAllUnaffected($date, $kind)
        ;
    }

    public function retrieveByChildId($childId, $from, $to) {

        $child = $this->em->getRepository('App\Entity\Child')->find($childId);
        $from = new DateTime($from);
        $to   = new DateTime($to);
        $results = [];
        $pickups = $this->em->getRepository('App\Entity\Pickup')->findByChildAndFromToDate($child, $from, $to);
        foreach($pickups as $pickup) {
            $results[$pickup->getStart()->format('Y-m')][$pickup->getStart()->format('W')][] = $this->toArray($pickup);
        }
        return $results;
    }

    /**
     * Geocodes all the Pickups.
     */
    public function geocode()
    {
        $counter = 0;
        $pickups = $this->em
            ->getRepository('App\Entity\Pickup')
            ->findGeocode()
        ;
        foreach ($pickups as $pickup) {
            if ($this->mainService->addCoordinates($pickup)) {
                $this->mainService->modify($pickup);
                $this->mainService->persist($pickup);
                ++$counter;
            }
        }

        return $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Pickup $object)
    {
        if (null === $object->getStart() ||
            null === $object->getAddress() ||
            null === $object->getChild()) {
            throw new UnprocessableEntityHttpException('Missing data for Pickup -> '.json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Pickup $object, string $data, $sender = null)
    {
        $status_original = $object->getStatus();

        //Submits data
        $data = $this->mainService->submit($object, 'pickup-modify', $data);

        //Suppress ride if set to null
        if (array_key_exists('ride', $data) && (null === $data['ride'] || 'null' === $data['ride'])) {
            $object->setRide(null);
        }

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Checks coordinates
        if (array_key_exists('address', $data)) {
            $this->checkCoordinates($object, true);
        }

        // update change status
        if ($status_original != $object->getStatus()) {
            $object->setStatusChange(new DateTime());

            // update presence and activity on cascade if npec or pec
            if ($object->getStatus() == 'npec' || $object->getStatus() == 'pec' || $object->getStatus() == null || $object->getStatus() == '') {
                
                // in case of dropoff should we change other status ?

                $this->childPresenceService->updateStatus($object->getChild(), $object->getStart(), $object->getStatus());
            }

            // modify other pickup same day & NOT SEND BY DRIVER
            if($sender != 'driver') {
                    $allpa = $this->em->getRepository('App\Entity\Pickup')->findAllByChildAndDate($object->getChild(), $object->getStart()->format('Y-m-d'));
                    if ($allpa) {
                        foreach ($allpa as $mypa) {
                            $mypa->setStatus($object->getStatus());
                            $mypa->setStatusChange(new DateTime());
                            $this->em->persist($mypa);
                            $this->em->flush();
                        }
                    }
            }
        }

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        $this->updatePickupActivity($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Pickup modifié',
            'pickup' => $this->toArray($object),
        );
    }

    public function updatePickupActivity($pickup)
    {
        //  if(!$activity = $this->em->getRepository('App\Entity\PickupActivity')->findBy(['child' => $pickup->getChild(), 'date' => $pickup->getDate()])) return null;
    //  $activity->setStatus($pickup->getStatus());

      //$this->mainService->modify($activity);
      //$this->mainService->persist($activity);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPEC($childId, $kind)
    {
        $child = $this->em->getRepository('App\Entity\Child')->find($childId);
        $pickup = $this->em
                ->getRepository('App\Entity\Pickup')->findOneBy(
                                                    array('child' => $child, 'kind' => $kind, 'status' => 'pec'),
                                                    array('start' => 'desc'),
                                                    1
                                                );
        if (!$pickup) {
            return null;
        }
        $result = [
                                'kind' => $pickup->getKind(),
                                'status_change' => $pickup->getStatusChange(),
                                'start' => $pickup->getStart(),
                                'status' => $pickup->getStatus(),
                    ];

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Pickup $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related registration
        if (null !== $object->getRegistration() && !$object->getRegistration()->getSuppressed()) {
            $objectArray['registration'] = $this->mainService->toArray($object->getRegistration()->toArray());
            
            if($product = $object->getRegistration()->getProduct()) {
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
                "registrationId" => $object->getRegistration()->getRegistrationId(),
                "hasLunch"       => $hasLunch,
                "productHours"   => $productHours,
                "productName"    => $productName,
                'productIsOffered' => $productIsOffered
            ];
            $objectArray['registrationData'] = $registrationData;

            if($sessions = $object->getRegistration()->getSessions() ) {
                foreach($sessions as $session) {
                    if($session['date'] == $object->getStart()->format('Y-m-d')) {
                        $objectArray['currentSession'] = $session;
                    }
                }
            }


        }

        //Gets related child
        if (null !== $object->getChild() && !$object->getChild()->getSuppressed()) {
            $objectArray['child'] = $this->mainService->toArray($object->getChild()->toArray());
        }

        //Gets related ride
        if (null !== $object->getRide() && !$object->getRide()->getSuppressed()) {
            $objectArray['ride'] = $this->mainService->toArray($object->getRide()->toArray());
        }

        return $objectArray;
    }

    /**
     * Unaffects all the pickups from rides for a specific date.
     */
    public function unaffect($date, $kind)
    {
        $counter = 0;
        $kinds = 'all' === $kind ? array('dropin', 'dropoff') : array($kind);
        foreach ($kinds as $kind) {
            $pickups = $this->findAllByDate($date, $kind);
            if (!empty($pickups)) {
                foreach ($pickups as $pickup) {
                    //Unaffects Pïckup if it's Ride is NOT locked
                    if (null !== $pickup->getRide() && !$pickup->getRide()->getLocked()) {
                        $pickup
                            ->setRide(null)
                            ->setSortOrder(null)
                            ->setStatus(null)
                            ->setStatusChange(new DateTime())
                        ;
                        $this->mainService->modify($pickup);

                        ++$counter;
                        if (20 === $counter) {
                            $this->em->flush();
                            $counter = 0;
                        }
                    }
                }
            }
        }

        $this->em->flush();
    }
}
