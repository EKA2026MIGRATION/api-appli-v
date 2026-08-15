<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PickupActivity;
use App\Entity\Pickup;
use App\Entity\Meal;
use App\Entity\ChildPresence;
use App\Entity\Registration;
use DateTime;

class CascadeService
{
    private $em;
    private $mainService;
    private $mealService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        MealServiceInterface $mealService,
        PickupActivityService $pickupActivityService,
        ProductService $productService
    ) {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->mealService = $mealService;
        $this->pickupActivityService = $pickupActivityService;
        $this->productService = $productService;
    }

    /**
     * create coordinate
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
     * cascade from invoice
     * load cascade by retrieve registration
     */
    public function cascadeFromInvoice($invoice) {

        // retrieve registrations from invoice
        if(!$registrations = $this->em->getRepository('App\Entity\Registration')->findByInvoice($invoice, 'payed')) return ['no registration'];


        foreach($registrations as $registration) {
            $messages[] = $this->cascadeFromRegistration($registration);

        }
        return $messages;
    

    }

    /**
     * cascade from registration
     * create childPresence
     */
    public function cascadeFromRegistration($registration) {

        $sessions = $registration->getSessions(); 

        // create each presence from date in sessions
        foreach($sessions as $session) {

            $date = new DateTime($session['date']);

            $messages[] = '------start session: '.$date->format('Y-m-d');


            $arr[$registration->getRegistrationId()][] = $session['date'];


            $startEl = explode(':', $session['start']);
            (!isset($startEl[2])) ? $start = $session['start'].':00' : $start = $session['start'];
            if(strlen($session['start']) > 10) $start = '00:00:00'; // ajouter 18/11/2021 - a garder ou aps .
            $startDate = DateTime::createFromFormat('H:i:s', $start);


            $endEl = explode(':', $session['end']);
            (!isset($endEl[2])) ? $end = $session['end'].':00' : $end = $session['end'];
            if(strlen($session['end']) > 10) $end = '00:00:00'; // ajouter 18/11/2021 - a garder ou aps .

            $endDate = DateTime::createFromFormat('H:i:s', $end);

            $currentSession = ['date' => $date, 'start' => $startDate, 'end' => $endDate];

            // check if presence exist
            $presenceData = ['date' => $date->format('Y-m-d'), 'child' => $registration->getChild()->getChildId()];

            $presenceExists = $this->em->getRepository('App\Entity\ChildPresence')->findByData($presenceData);

            // create presence if not exist
            if(!$presenceExists) {
                    $object = new ChildPresence();
                    $this->mainService->create($object);
            
                    $object->setRegistration($registration);
                    $object->setChild($registration->getChild());
                    $object->setLocation($registration->getLocation());
                    $object->setDate($currentSession['date']);
                    $object->setStart($currentSession['start']);
                    $object->setEnd($currentSession['end']);
                    $object->setStatus($registration->getStatus());

                    $this->mainService->persist($object);

                    $messages[] = 'presence '.$object->getChildPresenceId().' created';


                    // create pickup from a session
                    if($registration->getProduct()->getTransport() == 1) {
                        $messages[] = $this->createPickupFromSession($registration, $currentSession);
                    } else {
                        $messages[] = "no transport (pickup) created";
                    }

                    // create activity (pickupactivity from sport)
                    if (null !== $registration->getSports()) {
                        foreach ($registration->getSports() as $sportLink) {
                            if (!$sportLink->getSport()->getSuppressed()) {
                            $sport = $sportLink->getSport();
                            
                            $messages[] = $this->createPickupActivityFromSession($registration, $currentSession, $sport);
                                
                            }
                        }
                    } else {
                        $messages[] = "no pickupactivity (sport) created";
                    }
                

                    // create Meal from Child Presence
                    if($registration->getHasLunch() == 1 || $registration->getProduct()->getLunch() == 1) {
                        if($this->mealService->createMealFromChildPresence($object)) {
                            $messages[] = "meal created";


                            // create pickup activity lunch 
                            $lunch = $this->em->getRepository('App\Entity\Sport')->find(10);
                            $luncActivity = new PickupActivity();
                            $this->mainService->create($luncActivity);

                            $luncActivity->setRegistration($registration);
                            $luncActivity->setChild($registration->getChild());
                            $luncActivity->setDate($currentSession['date']);
                            $luncActivity->setStart($currentSession['start']);
                            $luncActivity->setEnd($currentSession['end']);
                            $luncActivity->setSport($lunch);
                            $luncActivity->setLocation($registration->getLocation());

                            //Persists data
                            $this->mainService->persist($luncActivity);

                            $messages[] = "lunch presence created";


                                        

                        }
                    } else {
                        $messages[] = "no meal created";
                    }
            } else {
                $messages[] = "childpresence already exists - cascasde stopped";




            } 

            $messages[] = '----end session';
        }

        return $messages;

    }

    /**
     * create 2 pîckup from data in session
     */
    public function createPickupFromSession($registration, $session) {


        // SET TIME DEFAUT
        $pickupsData = [
            'dropin' => $registration->getProduct()->getHourDropin(),
            'dropoff' => $registration->getProduct()->getHourDropoff()
        ];

        // SET ADDRESSE DEFAULT

        $message_address = "";
        if( $registration->getPreferences()) {

            // if no adress in pref
            if(!$addressPref = $this->em->getRepository('App\Entity\Address')->find($registration->getPreferences()[0]['address'])) {

                // chek if personn exist
                if($person = $registration->getPerson()) {

                    // retrieve address
                    foreach($person->getAddresses() as $addressLink) {
                        if(!$addressLink->getAddress()) {
                            $message_address = "not address founded";
                        } else {
                            $i = 0;
                            if (!$addressLink->getAddress()->getSuppressed()) {
                                $i++;
                                $addresses[] = $addressLink->getAddress();
                            }
                            if($i == 0) {
                                $message_address = "not address founded";
                            } else {
                                $addressPref = $addresses[0];
                            }
                        }

                    }
                } else {
                    $message_address = "not address founded";
                }
            }

            if(isset($addressPref)) {
                $message_address = "address added";
                $postal = $addressPref->getPostal();
                $fullAddress = $addressPref->getAddress().', '.$addressPref->getPostal().' '.$addressPref->getTown();
                $checkCoord = 1;
            } else {
                $message_address = "not address founded";
                $postal = "75000";
                $fullAddress = "AUCUNE ADRESSE TROUVEE PB";
                $checkCoord = 0;
            }

        } else { 
            $message_address = "not address founded";
            $postal = "75000";
            $fullAddress = "AUCUNE ADRESSE TROUVEE PB";
            $checkCoord = 0;
        }


        // SET PHONE DEFAULT
        if(isset($registration->getPreferences()[0]['phone']) && $phonePref = $this->em->getRepository('App\Entity\Phone')->find($registration->getPreferences()[0]['phone'])) {
            $telPhone = $phonePref->getPhone();
        } else {
            $telPhone = null;
        }

        $i = 0;
        foreach($pickupsData as $kind => $timePickup) {
            $i++;
            $start = new \DateTime($session['date']->format('Y-m-d').' '.$timePickup->format('H:i:s'));

            //Submits data
            $object = new Pickup();
            $this->mainService->create($object);

            $object->setChild($registration->getChild());
            $object->setKind($kind);
            $object->setStart($start);
            $object->setRegistration($registration);
            $object->setPhone($telPhone);
            $object->setPostal($postal);
            $object->setAddress($fullAddress);

            //Checks coordinates
            if($checkCoord == 1) $this->checkCoordinates($object);
            $this->mainService->persist($object);

            $arr[] = $object->getPickupId();
        }


        return 'pickup(s) created '.implode(', ', $arr).' - '.$message_address;

    }

    /**
     * Create pickupActivity from sessions
     */
    public function createPickupActivityFromSession($registration, $session, $sport) {


        // create pickup activity
        $object = new PickupActivity();
        $this->mainService->create($object);

        $object->setDate($session['date']);
        $object->setRegistration($registration);
        $object->setChild($registration->getChild());
        $object->setStart($session['start']);
        $object->setEnd($session['end']);
        $object->setSport($sport);
        $object->setLocation($registration->getLocation());

        //Persists data
        $this->mainService->persist($object);

        return $sport->getName().' added';
        
    }


    public function deleteChildPresence($childPresence, $return = true)
    {

        $child  = $childPresence->getChild();
        $mydate = $childPresence->getDate()->format('Y-m-d');

        // delete pickups
        $pickups = $this->em->getRepository('App\Entity\Pickup')->findAllByChildAndDate($child, $mydate);
        if (!empty($pickups)) {
            foreach ($pickups as $pickup) {
                $this->delete($pickup, false);
            }
        }

        // delete pickupactivity
        $pickupActivities = $this->pickupActivityService->findAllByChildDate($childPresence->getChild()->getChildId(), $mydate);
        if (!empty($pickupActivities)) {
            foreach ($pickupActivities as $pickupActivity) {
                if (!$pickupActivity->getGroupActivities()->isEmpty()) {
                    foreach ($pickupActivity->getGroupActivities() as $groupActivity) {
                        $this->em->remove($groupActivity);
                    }
                }
        
                $this->delete($pickupActivity, false);
            }
        }

        // delete meals
        $this->mealService->deleteFromChildPresence($childPresence);

        //Persists data
        $this->mainService->delete($childPresence);
        $this->mainService->persist($childPresence);

        return $return;
    }

    public function delete($object) {
        $this->mainService->delete($object);
        $this->mainService->persist($object);

    }
}
