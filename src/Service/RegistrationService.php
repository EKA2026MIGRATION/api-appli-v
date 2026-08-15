<?php

namespace App\Service;

use App\Entity\Registration;
use App\Entity\RegistrationSportLink;
use App\Entity\Sport;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Service\CascadeService;


/**
 * RegistrationService class.
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RegistrationService implements RegistrationServiceInterface
{
    private $em;

    private $mainService;

    private $productService;

    private $childPresenceService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        ProductServiceInterface $productService,
        ChildPresenceService $childPresenceService,
        CascadeService $cascadeService,
        PersonService $personService,
        PickupService $pickupService
    ) {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->productService = $productService;
        $this->ChildPresenceService = $childPresenceService;
        $this->cascadeService = $cascadeService;
        $this->personService = $personService;
        $this->pickupService = $pickupService;
    }

    /**
     * Adds specific data that could not be added via generic method.
     */
    public function addSpecificData(Registration $object, array $data)
    {
        //Adds registration datetime
        if (null === $object->getRegistration()) {
            $object->setRegistration(new DateTime());
        }

        //Adds preferences
        if (array_key_exists('preferences', $data)) {
            $object->setPreferences(serialize($data['preferences']));
        }

        //Adds sessions
        if (array_key_exists('sessions', $data)) {
            $object->setSessions(serialize($data['sessions']));
        }

        //Adds sports
        if (array_key_exists('sports', $data)) {
            //Removes old links
            $this->removeSportsLinks($object);

            //Adds new links
            foreach ($data['sports'] as $sport) {
                $this->addSportLink($sport['sportId'], $object);
            }
        }
    }

    /**
     * Adds link between Registration and Sport.
     */
    public function addSportLink(int $sportId, Registration $object)
    {
        $sport = $this->em->getRepository('App\Entity\Sport')->findOneById($sportId);
        if ($sport instanceof Sport && !$sport->getSuppressed()) {
            $registrationSportLink = new RegistrationSportLink();
            $registrationSportLink
                ->setRegistration($object)
                ->setSport($sport)
            ;
            $this->em->persist($registrationSportLink);
        }
    }

 

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {

        $dataArray2 = is_array($data) ? $data : json_decode($data, true);

        $data = $dataArray2;

        if(isset($data['freeAddress'])) unset($data['freeAddress']);
        if(isset($data['freePostal'])) unset($data['freePostal']);
        if(isset($data['freeTown'])) unset($data['freeTown']);
        if(isset($data['pickupDatePaiement'])) unset($data['pickupDatePaiement']);

        $child   = $this->em->getRepository('App\Entity\Child')->find($data['child']);
        $product = $this->em->getRepository('App\Entity\Product')->find($data['product']);

        // check if a registreation exist
        $exist = 0; $dateExist = [];
        if(isset($data['sessions'])) {
                foreach($data['sessions'] as $session) {
                    $date = $session['date'];
                    $existRegistrations = $this->em->getRepository('App\Entity\Registration')->findRegistrationsByData($child, $date);

                    foreach($existRegistrations as $registration) {
                        $exist++;
                        if(in_array($date, $dateExist)) continue;
                        $element = explode('-', $date);
                        $date_fr = $element[2].'/'.$element[1].'/'.$element[0];
                        $messages[] = ['name' => $child->getFullname(), 'date_fr' => $date_fr, 'date_en' => $date];
                        $dateExist[] = $date;
                        
                    }
                }
        }

        if($exist > 0) {
            //Returns data
            return array(
                'status' => "fail",
                'message' => 'another_registration_exist',
                'informations' => $messages,
            );
        }



        if(isset($data['registrationid']) && isset($data['person'])) {
            $registrationAwaiting = $this->em->getRepository('App\Entity\Registration')->find($data['registrationid']);
            $person = $this->em->getRepository('App\Entity\Person')->find($data['person']);
        } else {
            $registrationAwaiting = null;
            $person = null;
        }


        if($registrationAwaiting && $person) {


            $registrationAwaiting->setStatus('cart');
            $registrationAwaiting->setPerson($person);
            $this->mainService->persist($registrationAwaiting);

            $message = "registration ".$registrationAwaiting->getRegistrationId()." status set to cart";

            $object = $registrationAwaiting;

        } else {


            //Submits data
            $object = new Registration();

            $this->mainService->create($object);

            $data = $this->mainService->submit($object, 'registration-create', $data);

            $this->addSpecificData($object, $data);


            if($object->getPreferences() == null) {
                $dataArray = is_array($data) ? $data : json_decode($data, true);
                if(isset($dataArray['address'])) {

                    if($addressPref = $this->em->getRepository('App\Entity\Address')->find($dataArray['address'])) {
                        $arr[0]['address'] = $dataArray['address'];
                        $arr[0]['postal']  = $addressPref->getPostal();

                        $object->setPreferences(serialize($arr));
                    }
                }

            }

            //Checks if entity has been filled
            $this->isEntityFilled($object);

            //Persists data
            $this->mainService->persist($object);

            if($object->getStatus() != "cart") {
                $message = $this->cascadeService->cascadeFromRegistration($object);
            } else {
                $message = "registration ".$object->getRegistrationId()." created in cart status";
            }


            /*** UPDATE FREE ADDRESS ON PICKUP */

            // update free address if exist
            if(isset($dataArray2['freeAddress'])) {
                if($dataArray2['freeAddress'] != "" && $dataArray2['freePostal'] != ""  &&   $dataArray2['freeTown']) {
                    $pickups = $this->em->getRepository('App\Entity\Pickup')->findBy(['registration' => $object]);
                    if (!empty($pickups)) {
                        foreach ($pickups as $pickup) {
                            $pickup->setAddress($dataArray2['freeAddress'].' - '.$dataArray2['freePostal'].' - '.$dataArray2['freeTown']);
                            $pickup->setPostal($dataArray2['freePostal']);
                            $this->pickupService->checkCoordinates($pickup);
                            $this->mainService->persist($pickup);
                        }
                    }
                }

            }


            /*** UPDATE PAIEMENT  */

            if(isset($dataArray2['pickupDatePaiement']))  {
                if($dataArray2['pickupDatePaiement'] != "") {

                    $el = explode(',' , $dataArray2['pickupDatePaiement']);

                    if(!isset($el[1])) {
                        $arr = [$dataArray2['pickupDatePaiement']];
                    } else {
                        $arr = $el;
                    }

                    foreach($arr as $a) {
                        $elements = explode('|', $a);

                        $price = $elements[0];
                        $date  = $elements[1];

                        $pickups = $this->em->getRepository('App\Entity\Pickup')->findByRegistrationAndDate($date, $object->getRegistrationId());
                        foreach ($pickups as $pickup) {
                            if($pickup->getKind() == "dropin") {
                                $pickup->setPaymentDue($price);
                                $this->mainService->persist($pickup);
                            }
                        }
                    }
                }
            }


        }



        //Returns data
        return array(
            'status' => true,
            'message' => 'Inscription ajoutée',
            'messages' => $message,
            'registration' => $this->toArray($object),
        );
    }


    public function bulkUpdateStatus($data) {

        $datas = is_array($data) ? $data : json_decode($data, true);

        $result = [];
        foreach($datas['registrationIds'] as $registration_id) {

            $registration = $this->em->getRepository('App\Entity\Registration')->find($registration_id);
            $registration->setStatus('payed');
            $this->mainService->persist($registration);
            $result[] = $this->toArray($registration);
        }

        return array(
            'status' => true,
            'result' => $result,
            'ids' => $datas['registrationIds'] ,
        );

    }

    /**
     * {@inheritdoc}
     */
    public function delete(Registration $object)
    {
        $object->setStatus('delete');
        $this->mainService->persist($object);

        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        //Deletes links to sports
        $this->removeSportsLinks($object);

        return array(
            'status' => true,
            'message' => 'Inscription supprimée',
        );
    }


    public function awaitingPayment($from = null, $to = null) {


        if(!$registrations = $this->em->getRepository('App\Entity\Registration')->findAwaiting()) return ['message' => 'no regisrations founded'];

        foreach($registrations as $registration) {

            $objectArray = $this->mainService->toArray($registration->toArray());

            if (null !== $registration->getChild() && !$registration->getChild()->getSuppressed()) {
                $child = $registration->getChild();
                $objectArray['child'] = [
                                            'fullname' => $child->getFullnameReverse(),
                                            'id'=> $child->getChildId()
                                        ];

                $childList[$child->getChildId()] = $child->getFullnameReverse();
            }

            if (null !== $registration->getPerson() && !$registration->getPerson()->getSuppressed()) {
                $person = $registration->getPerson();
                $objectArray['person'] = [
                                            'fullname' => $person->getFirstname().' '.$person->getLastname()
                                    ];
            } else {

                if($person = $this->personService->findByUserId($registration->getCreatedBy())) {
                    $objectArray['person'] = ['fullname' => $person->getFirstname().' '.$person->getLastname()];
                }
            }

            if (null !== $registration->getProduct() && !$registration->getProduct()->getSuppressed()) {
                $product = $registration->getProduct();
                $objectArray['product'] = [
                                            'name' => trim(strip_tags($product->getNameFr())), 'priceTtc' => $product->getPriceTtc()];
            }

            unset($objectArray['location'], $objectArray['sports'], $objectArray['address']);

            $result[] = $objectArray;
        }

        asort($childList);

        $arr = [
                    'registrations' => $result,
                    'childList' => $childList
                ];

        return $arr;

    }


    /**
     * Returns the list of all registrations related to status in the array format.
     */
    public function findAllByStatus($status)
    {
        return $this->em
            ->getRepository('App\Entity\Registration')
            ->findAllByStatus($status)
        ;
    }

    /**
     * Returns the list of all registrations related to person and status in the array format.
     */
    public function findAllByPersonAndStatus($personId, $status)
    {
        $registrations = $this->em
            ->getRepository('App\Entity\Registration')
            ->findAllByPersonAndStatus($personId, $status)
        ;

        $registrationsArray = array();
        $i = 0;
        foreach ($registrations as $registration) {

            $i++;


            if($registration->getSessions()) {
                foreach($registration->getSessions() as $session) {
                    if(isset($session['date'])) {
                        $currentDate = str_replace('-', '', $session['date']);
                        $dateSession[] = $currentDate;
                    }
                }
    
                if(isset($dateSession)) {
                    asort($dateSession);
                    $ref = $dateSession[0].$i;
                } else {
                    $ref = $i;
                }
            } else {
                $ref = $i;
            }

            $registrationsArray[$ref] = $this->toArray($registration);
        };

        krsort($registrationsArray);

        return $registrationsArray;
    }



    /**
     * Returns the list of all registrations related to child .
     */
    public function findAllByChildStatus($childId, $status)
    {

        $child = $this->em->getRepository('App\Entity\Child')->find($childId);

        $registrations = $this->em->getRepository('App\Entity\Registration')->findBy(['child' => $child, 'status' => $status]);

        $i = 0;
        $registrationsArray = array();
        foreach ($registrations as $registration) {
            if($registration->getSuppressed() == 1) continue;
            $registrationsArray[] = $this->toArray($registration);
            $i++;
        };

        return $registrationsArray;

    }


    /**
     * Returns the list of regisration by child from date to date
     */
    public function findAllByChild($childId, $from, $to) {

        $child = $this->em->getRepository('App\Entity\Child')->find($childId);
        $from = new DateTime($from);
        $to   = new DateTime($to);
        if($registrations = $this->em->getRepository('App\Entity\Registration')->findAllByChild($child, $from, $to)) {
            foreach($registrations as $registration) {
                $result[] = $this->toArray($registration);
            }
        } else {
            $result = null;
        }
        
       
        return $result;

    }

    /**
     * Returns the list of all registrations related to person without the cart status in the array format.
     */
    public function findAllWithoutCart()
    {
        return $this->em
            ->getRepository('App\Entity\Registration')
            ->findAllWithoutCart()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Registration $object)
    {
        if (null === $object->getChild() ||
            null === $object->getProduct()) {
            throw new UnprocessableEntityHttpException('Missing data for Registration -> '.json_encode($object->toArray()));
        }
    }


    /**
     * {@inheritdoc}
     */
    public function updateStatus(string $data)
    {

        $data = json_decode($data, true);

        $registration = $this->em->getRepository('App\Entity\Registration')->find($data['registrationid']);


        $registration->setStatus($data['status']);
        $registration->setPaymentType($data['type']);
        $registration->setPayed($data['amount']);
        $registration->setPaymentInfo($data['info']);


        //Persists data
        $this->mainService->modify($registration);
        $this->mainService->persist($registration);

        return array(
            'status' => true,
            'message' => 'Inscription modifiée',
            'registration' => $this->toArray($registration),
        );



    }

    /**
     * {@inheritdoc}
     */
    public function modify(Registration $object, string $data)
    {

        $firstStatus = $object->getStatus();

        //Submits data
        $data = $this->mainService->submit($object, 'registration-modify', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);


        $message = "";  
        if($firstStatus == "cart" && $object->getStatus() == "payed") {
            $arr[] = 'in';
            $message = $this->cascadeService->cascadeFromRegistration($object);
        } else {
            $arr[] = 'out';
        }

        //Returns data
        return array(
            'status' => true,
            'cascade' => $message,
            'message' => 'Inscription modifiée',
            'registration' => $this->toArray($object),
        );
    }

    /**
     * Removes links from Registration.
     */
    public function removeSportsLinks(Registration $object)
    {
        //Removes links to sports
        if (!$object->getSports()->isEmpty()) {
            foreach ($object->getSports() as $link) {
                $this->em->remove($link);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Registration $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related child
        if (null !== $object->getChild() && !$object->getChild()->getSuppressed()) {
            $objectArray['child'] = $this->mainService->toArray($object->getChild()->toArray());
        }

        //Gets related person
        if (null !== $object->getPerson() && !$object->getPerson()->getSuppressed()) {
            $objectArray['person'] = $this->mainService->toArray($object->getPerson()->toArray());
        } else {
            if($person = $this->personService->findByUserId($object->getCreatedBy())) {
                $objectArray['person'] = $this->mainService->toArray($person->toArray());
            }
        }

        if($object->getInvoice() !== null && is_object($object->getInvoice())){
            $objectArray['invoice'] = $object->getInvoice()->toArray();
        }

        //Gets related product
        if (null !== $object->getProduct() && !$object->getProduct()->getSuppressed()) {
            $objectArray['product'] = $this->productService->toArray($object->getProduct());
        } 

        //Gets related location
        if (null !== $object->getLocation() && !$object->getLocation()->getSuppressed()) {
            $objectArray['location'] = $this->mainService->toArray($object->getLocation()->toArray());
        }

        //Gets related sports
        if (null !== $object->getSports()) {
            $sports = array();
            foreach ($object->getSports() as $sport) {
                if (!$sport->getSport()->getSuppressed()) {
                    $sports[] = $this->mainService->toArray($sport->getSport()->toArray());
                }
            }
            $objectArray['sports'] = $sports;
        }

        //Gets related transaction
        if (null !== $object->getTransaction() && !$object->getTransaction()->getSuppressed()) {
            $objectArray['transaction'] = $this->mainService->toArray($object->getTransaction()->toArray());
        }

        return $objectArray;
    }
}
