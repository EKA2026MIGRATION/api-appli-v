<?php

namespace App\Service;


use App\Entity\ChildPresence;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Entity\PickupActivity;
use App\Entity\Pickup;
use App\Entity\Meal;
use App\Service\CascadeService;

use DateTimeInterface;

/**
 * ChildPresenceService class.
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ChildPresenceService implements ChildPresenceServiceInterface
{
    private $em;
    private $childService;
    private $mainService;
    private $pickupActivityService;
    private $mealService;
    private $cascadeService;

    public function __construct(
        EntityManagerInterface $em,
        ChildServiceInterface $childService,
        MainServiceInterface $mainService,
        PickupActivityServiceInterface $pickupActivityService,
        MealServiceInterface $mealService,
        CascadeService $cascadeService
    ) {
        $this->em = $em;
        $this->childService = $childService;
        $this->mainService = $mainService;
        $this->pickupActivityService = $pickupActivityService;
        $this->mealService = $mealService;;
        $this->cascadeService = $cascadeService;
    }

    /**
     * Adds specific data that could not be added via generic method.
     */
    public function addSpecificData(ChildPresence $object, array $data)
    {
        //Should be done from ChildPresenceType but it returns null...
        if (array_key_exists('start', $data)) {
            $object->setStart(DateTime::createFromFormat('H:i:s', $data['start']));
        }
        if (array_key_exists('end', $data)) {
            $object->setEnd(DateTime::createFromFormat('H:i:s', $data['end']));
        }
    }



    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $data = json_decode($data, true);
        if (is_array($data) && !empty($data)) {
            foreach ($data as $childPresence) {
                $object = $this->em->getRepository('App\Entity\ChildPresence')->findByData($childPresence);
                //Creates object if not already existing
                if (null === $object) {
                    $object = new ChildPresence();
                    $this->mainService->create($object);

                    //Submits data
                    $this->mainService->submit($object, 'child-presence-create', $childPresence);
                    $this->addSpecificData($object, $childPresence);

                    //Checks if entity has been filled
                    $this->isEntityFilled($object);

                    //Persists data
                    $this->mainService->persist($object);

                    // create Meal from Child Presence
                    $this->createMealFromChildPresence($object);
                }
            }

            //Returns data
            return array(
                'status' => true,
                'message' => 'ChildPresence ajoutées',
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> '.json_encode($data));
    }

    public function createMealFromChildPresence($object) {
        if(!$object->getRegistration()->getHasLunch()) return null;
        return $this->mealService->createMealFromChildPresence($object);
    }


    public function updateLastDayOfWeek($currentDateString) {

        (!$currentDateString) ? $currentDate = date('Y-m-d') : $currentDate = $currentDateString;

        // find the sunday of the week
        $sunday = date("Y-m-d", strtotime("next sunday", strtotime($currentDate)));

        // retrieve all child presence on current day
        $childPresences = $this->em->getRepository(('App\Entity\ChildPresence'))->findBy(['date' => new DateTime($currentDate)]);

        if(!$childPresences) return 'no_child_presence_on_'.$currentDate;

        // loop on each child presence
        foreach($childPresences as $presence) {

            // get child 
            $child = $presence->getChild();

            // search child presece >= currentDate and <= sunday of week
            $lastPresence = $this->em->getRepository('App\Entity\ChildPresence')->findPresenceBetween($child, $currentDate, $sunday);

            // update lastPresence with the lastDay
            if($lastPresence) {

                // set null to current date
                $presence->setLastDayOfWeek(null);
                $this->em->persist($presence);
                $this->em->flush($presence);


                // set lastPresence the date
                $lastPresence->setLastDayOfWeek($lastPresence->getDate());
                $this->em->persist($lastPresence);
                $this->em->flush($lastPresence);



                /***** update pickup */
                // retrieve pickup from presence and update
                if($presence->getDate() != null) {
                
                    if ($pickups = $this->em->getRepository('App\Entity\Pickup')->findByChildAndDate($child, $presence->getDate()->format('Y-m-d'))) {
                        
                        foreach ($pickups as $p) {
                            $p->setLastDayOfWeek(null);
                            $this->em->persist($p);
                            $this->em->flush();
                        }
                    }
                }

                if($lastPresence->getDate() != null) {
                    if ($pickups = $this->em->getRepository('App\Entity\Pickup')->findByChildAndDate($child, $lastPresence->getDate()->format('Y-m-d'))) {
                        
                        foreach ($pickups as $p) {
                            $p->setLastDayOfWeek($lastPresence->getDate());
                            $this->em->persist($p);
                            $this->em->flush();
                        }
                    }
                }
              



                /***** update pickupactivity */
                // retrive pickup from lastpresence and update
                if ($pickupActivitys = $this->em->getRepository('App\Entity\PickupActivity')->findBy(['child' => $child, 'date' => $presence->getDate()])) {
                    
                    foreach ($pickupActivitys as $pa) {
                        $pa->setLastDayOfWeek(null);
                        $this->em->persist($pa);
                        $this->em->flush();
                    }
                }
                if ($pickupActivitys = $this->em->getRepository('App\Entity\PickupActivity')->findBy(['child' => $child, 'date' => $lastPresence->getDate()])) {
                    
                    foreach ($pickupActivitys as $pa) {
                        $pa->setLastDayOfWeek($lastPresence->getDate());
                        $this->em->persist($pa);
                        $this->em->flush();
                    }
                }
  
            }
    
        }

        return ['status' => 'last day of week updated for '.$currentDate];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ChildPresence $object, $return = true)
    {
        //cascade
        if($object->getRegistration()) {
            $return = $this->cascadeService->deleteChildPresence($object);
        } 

        $this->mainService->delete($object);
        $this->mainService->persist($object);


        if ($return) {
            return array(
                'status' => true,
                'message' => 'ChildPresence supprimée',
            );
        }
    }

    public function deleteByArrayStringList($idsList) {
        $data = explode(',', $idsList);

        foreach ($data as $childPresence) {
            $childPresence = $this->em->getRepository('App\Entity\ChildPresence')->find($childPresence);
            if ($childPresence instanceof ChildPresence) {
                $this->delete($childPresence, false);
            }
        }
        return $data;
    }
    

    /**
     * Deletes ChildPresence by array of ids.
     */
    public function deleteByArray(string $data)
    {

        $data = json_decode($data, true);

        if (is_array($data) && !empty($data)) {
            foreach ($data as $childPresence) {
                $childPresence = $this->em->getRepository('App\Entity\ChildPresence')->findByData($childPresence);
                if ($childPresence instanceof ChildPresence) {
                    $this->delete($childPresence, false);
                }
            }

            return array(
                'status' => true,
                'message' => 'ChildPresence supprimées',
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> '.json_encode($data));
    }

    /**
     * Deletes ChildPresence by registrationId.
     */
    public function deleteByRegistrationId(int $registrationId)
    {
        $childPresences = $this->em->getRepository('App\Entity\ChildPresence')->findByRegistrationId($registrationId);
        if (!empty($childPresences)) {
            foreach ($childPresences as $childPresence) {
                $this->delete($childPresence, false);
            }

            return array(
                'status' => true,
                'message' => 'ChildPresence supprimées',
            );
        }
    }

    public function findByChildBetweenDates($childId, $from, $to) {

        $child = $this->em->getRepository('App\Entity\Child')->find($childId);
        $from = new DateTime($from);
        $to   = new DateTime($to);
        if($childPresences = $this->em->getRepository('App\Entity\ChildPresence')->findByChildBetweenDates($child, $from, $to)) {
            foreach($childPresences as $childPresence) {
                $result[$childPresence->getDate()->format('Y-m').'-01'][] = $this->toArray($childPresence);
            }
        } else {
            $result = null;
        }
        
        return $result;
    }

    public function findAllWeekPresences($monday) {
        $currentDate = $monday;
        $presences = [];
        $myarr = [];
        $childPresencesArray = null;
        for($i = 0; $i < 7; $i++) {
            $childPresencesArray = $this->findAllByDate($currentDate);
            $presences[$currentDate] = $childPresencesArray;
            unset($childPresencesArray);
            $currentDate = date('Y-m-d', strtotime($currentDate.", +1 day"));
        }

        return $presences;

    }



    /**
     * Returns the list of all children presence by date.
     *
     * @return array
     */
    public function findAllByDate($date)
    {

        $childPresences = $this->em->getRepository('App\Entity\ChildPresence')->findAllByDate($date);

        if(!$childPresences) return null;


        foreach ($childPresences as $childPresence) {
            if($childPresence->getChild()) {
                $child = $childPresence->getChild();

                // if registration founded
                if($registration = $childPresence->getRegistration()) {
                    $registrationId = $registration->getRegistrationId();
                    $registrationStatus = $registration->getStatus();

                    // sports
                    if($registration->getSports()) {
                        $sports = "unknown";
                        $nbSport = 0;
                        $sportsArray = [];
                        foreach ($registration->getSports() as $sport) {
                            if (!$sport->getSport()->getSuppressed()) {
                                $sportsArray[trim($sport->getSport()->getName())] = trim($sport->getSport()->getName());
                            }
                        }

                        if(isset($sportsArray)) {

                            // place le multiposrt uniuqement pour les <7 ans et supprime les autres sports
                            if(array_key_exists('Multisport', $sportsArray)) {
                                if( $child->getAge() > 6) {
                                    unset($sportsArray['Multisport']);
                                } else {
                                    $sportsArray = ['Multisport'];
                                }
                            }
                            asort($sportsArray);
                            $sports = implode(',', $sportsArray);
                            $nbSport = count($sportsArray);
                        }

                    } else {
                        $sports ="unknown";
                        $nbSport = 0;
                    }

                    // product
                    if($product = $registration->getProduct()) {
                        $productName = strip_tags($product->getNameFr());
                        $productIsOffered = $product->getIsOffered();
                        $hasTransport = $product->getTransport();
                        $category = $product->getCategories()[0]->getCategory()->getName();
                        $isHourSelectable = $product->getIsHourSelectable();
                    } else {
                        $hasTransport = "unknown";
                        $category = "unknown";
                        $productName = "unknown";
                        $isHourSelectable = "unknown";
                        $productIsOffered = null;
                    }

                // no registration
                } else {
                    $registrationId = "unknown";
                    $registrationStatus = "unknown";
                    $hasTransport = "unknown";
                    $category = "unknown";
                    $sports = "unknown";
                    $nbSport = 0;
                    $productName = "unknown";
                    $isHourSelectable = "unknown";
                    $productIsOffered = null;

                }
                // if last days week non
                if($childPresence->getLastDayOfWeek() != null) {
                    $lastDayOfWeek = $childPresence->getLastDayOfWeek()->format('Y-m-d');
                } else {
                    $lastDayOfWeek = null;
                }
 
                

                // pickup associated
                if($pickups = $this->em->getRepository('App\Entity\Pickup')->findByChildAndDate($child,$date)) {
                    $paymentDue = "unknown";
                    $paymentDone = "unknown";
                    foreach($pickups as $pickup) {
                        if($pickup->getPaymentDue() > 0) {
                            $paymentDue = $pickup->getPaymentDue();
                            $paymentDone = $pickup->getPaymentDone();
                            
                        }
                    }


                } else {

                    if($childPresence->getRegistration()) {
                             // if product has no transport
                            if(!$registration->getProduct()->getTransport())  {
                                if($childPresence->getRegistration()->getStatus() == "unpayed" || $childPresence->getRegistration()->getStatus() == "waiting" ) {
                                    $paymentDue = $registration->getProduct()->getPriceTtc();
                                    $paymentDone = $childPresence->getRegistration()->getPayed();
                                } else {
                                    $paymentDue = "unknown";
                                    $paymentDone = "unknown";
                                }
                            } else {
                                $paymentDue = "unknown";
                                $paymentDone = "unknown";
                            }


                    } else {
                        $paymentDue = "unknown";
                        $paymentDone = "unknown";
                    }
                }


                ($childPresence->getStart()) ? $start = $childPresence->getStart()->format('H:i:s') : $start = null;
                ($childPresence->getEnd()) ? $end = $childPresence->getEnd()->format('H:i:s') : $end = null;

                ($child->getDateLatestMedia()) ? $dateLatestMedia = $child->getDateLatestMedia()->format('Y-m-d') : $dateLatestMedia = null;


                if($childPresence->getLocation() != null) {
                    $locationName = $childPresence->getLocation()->getName();
                } else {
                    $locationName = "location unknown";
                }

                if($childPresence->getLocation() != null) {
                    $locationId = $childPresence->getLocation()->getLocationId();
                } else {
                    $locationId = $childPresence->getChildPresenceId();
                }

              
                $childPresencesArray[] = [
                                            'start' => $start,
                                            'end'   => $end,
                                            'childId' => $child->getChildId(),
                                            'urlPhoto' => $child->getPhoto(),
                                            'age' => $child->getAge(),
                                            'birthdate' => $child->getBirthdate()->format('Y-m-d'),
                                            'firstname' => $child->getFirstname(),
                                            'lastname'  => $child->getLastname(),
                                            'dateLatestMedia' => $dateLatestMedia,
                                            'childPresenceId' => $childPresence->getChildPresenceId(),
                                            'registrationid' => $registrationId,
                                            'lastDayOfWeek' => $lastDayOfWeek,
                                            'status'   => $childPresence->getStatus(),
                                            'hasTransport' => $hasTransport,
                                            'registrationStatus' => $registrationStatus,
                                            'paymentDue' => $paymentDue,
                                            'paymentDone' => $paymentDone,
                                            'location' => $locationName,
                                            'locationId' => $locationId,
                                            'category' => $category,
                                            'sports' => $sports,
                                            'nbSport' => $nbSport,
                                            'productName' => $productName,
                                            'productIsOffered' => $productIsOffered,
                                            'isHourSelectable' => $isHourSelectable
                ];

            } else {
                $childPresencesArray[] = [
                    'childPresenceId' => $childPresence->getChildPresenceId(),
                    'registrationid' => $childPresence->getRegistration()->getRegistrationId()
                ];
            }
        };

        return $childPresencesArray;
    
        
    }



    /**
     * Returns the list of presence by child.
     *
     * @return array
     */
    public function findByChild($childId, $date)
    {
        return $this->em
            ->getRepository('App\Entity\ChildPresence')
            ->findByChild($childId, $date)
        ;
    }


    public function findByLatestCreated($childId) { 
        return $this->em
        ->getRepository('App\Entity\ChildPresence')
        ->findLatestCreatedByChildId($childId)
    ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(ChildPresence $object)
    {
        if (null === $object->getChild() ||
            null === $object->getDate() ||
            null === $object->getStart() ||
            null === $object->getLocation()) {
            throw new UnprocessableEntityHttpException('Missing data for ChildPresence -> '.json_encode($object->toArray()));
        }
    }

    public function updateStatusOld($child, $date, $status) // to remove if ok after august 2024
    {
        $presence = $this->em->getRepository('App\Entity\ChildPresence')->findOneBy(['child' => $child, 'date' => $date]);
        if ($status == null) {
            $status = '';
        }
        $presence->setStatus($status);
        $presence->setStatusChange(new DateTime());

        $this->em->persist($presence);
        $this->em->flush();

        if ($pickupActivitys = $this->em->getRepository('App\Entity\PickupActivity')->findBy(['child' => $child, 'date' => $date])) {
            foreach ($pickupActivitys as $pa) {
                $pa->setStatus($status);
                $pa->setStatusChange(new DateTime());
                $this->em->persist($pa);
                $this->em->flush();
            }
        }

        return true;
    }

    public function updateStatus($child, $date, $status)
    {

        $child_id = $child->getChildId();
        $date = $date->format('Y-m-d');
        if ($status == null) { $status = '';}

        // update child presence
        $rawSql = ' UPDATE child_presence SET status = :status, status_change = NOW() WHERE child_id = :child_id AND date like :date';
        $stmt = $this->em->getConnection()->prepare($rawSql);
        $stmt->execute(['status' => $status, 'child_id' => $child_id, 'date' => "%".$date."%"]);

        // update pickup activity
        $rawSql = ' UPDATE pickup_activity SET status = :status, status_change = NOW() WHERE child_id = :child_id AND date like :date';
        $stmt = $this->em->getConnection()->prepare($rawSql);
        $stmt->execute(['status' => $status, 'child_id' => $child_id, 'date' => "%".$date."%"]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(ChildPresence $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());


        //Gets related registration
        if (null !== $object->getRegistration() && !$object->getRegistration()->getSuppressed()) {
            $objectArray['registration'] = $this->mainService->toArray($object->getRegistration()->toArray());

            if($product = $object->getRegistration()->getProduct()) {
                $objectArray['category'] = $product->getCategories()[0]->getCategory()->getName();
                $objectArray['product_name'] = strip_tags($product->getNameFr());
                $objectArray['product_is_offered'] = $product->getIsOffered();
            } else {
                $objectArray['category'] = "unknown";
            }
        } else {
            $objectArray['category'] = "unknown";
        }



        //Gets related child
        if (null !== $object->getChild() && !$object->getChild()->getSuppressed()) {
            $objectArray['child'] = $this->mainService->toArray($object->getChild()->toArray());
        }

        //Gets related person
        if (null !== $object->getPerson() && !$object->getPerson()->getSuppressed()) {
            $objectArray['person'] = $this->mainService->toArray($object->getPerson()->toArray());
        }

        //Gets related location
        if (null !== $object->getLocation() && !$object->getLocation()->getSuppressed()) {
            $objectArray['location'] = $this->mainService->toArray($object->getLocation()->toArray());
        }

        return $objectArray;
    }
}
