<?php

namespace App\Service;

use App\Entity\Child;
use App\Entity\ChildChildLink;
use App\Entity\ChildPersonLink;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\NotificationService;
use \PDO;


use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * ChildService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ChildService implements ChildServiceInterface
{
    private $em;

    private $mainService;

    private $personService;

    private $notificationService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        PersonServiceInterface $personService,
        NotificationService $notificationService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->personService = $personService;
        $this->notificationService = $notificationService;
    }

    /**
     * Adds link between Child and Person
     */
    public function addLinks(Child $object, array $data)
    {
        if (array_key_exists('links', $data)) {
            $this->removeLinks($object);
            if (is_array($data['links']) && !empty($data['links'])) {

                foreach ($data['links'] as $link) {
        
                    $person = $this->em->getRepository('App\Entity\Person')->findOneById($link['personId']);
                    if ($person instanceof Person && !$person->getSuppressed()) {
                        $childPersonLink = new ChildPersonLink();
                        $childPersonLink
                            ->setRelation(htmlspecialchars($link['relation']))
                            ->setChild($object)
                            ->setPerson($person)
                        ;
                        $this->em->persist($childPersonLink);
                    }
                }
            }
        }
    }

    /**
     * Adds link between Child and Child
     */
    public function addSiblings(Child $object, array $data)
    {
        if (array_key_exists('siblings', $data)) {
            $this->removeSiblings($object);
            if (is_array($data['siblings']) && !empty($data['siblings'])) {
                foreach ($data['siblings'] as $sibling) {
                    $child = $this->em->getRepository('App\Entity\Child')->findOneById($sibling['siblingId']);
                    if ($child instanceof Child && !$child->getSuppressed()) {
                        $childChildLink = new ChildChildLink();
                        $childChildLink
                            ->setRelation(htmlspecialchars($sibling['relation']))
                            ->setChild($object)
                            ->setSibling($child)
                        ;
                        $this->em->persist($childChildLink);
                    }
                }
            }
        }
    }

    public function retrieveChildMediaAdded($date) {

        // retrieve all childs where date_latest_media_date = $date
        $currentDate = new \DateTime($date);
        $childs = $this->em->getRepository('App\Entity\Child')->findBy(['dateLatestMedia' => $currentDate]);

        $arr = [];
        foreach($childs as $child) {
            $arr[] = $this->toArray($child);
        }
        return $arr;

    }

    public function listBySchool($name = null) {

        if(!$childs = $this->em->getRepository('App\Entity\Child')->listBySchool($name)) return null;

        $verifList = [];
        foreach($childs as $child) {
            $school = $child->getSchool();


            if(key_exists($school->getName(), $verifList)) {
                $id = $verifList[$school->getName()];
            } else {
                $verifList[$school->getName()] = $school->getSchoolId();
                $id = $school->getSchoolId();
            }


            $childBySchool[$id][] = [
                                        'fullname' =>strtoupper($child->getlastname()).' '.$child->getFirstname(),
                                        'age' => $child->getAge()
                                ];
            $schoolData[$id] = $school->toArray();
        }
        return ['childs' => $childBySchool, 'schools' => $schoolData];


    }

    /**
     * Adds specific data that could not be added via generic method
     */
    public function addSpecificData(Child $object, array $data)
    {
        $this->addLinks($object, $data);
        $this->addSiblings($object, $data);

        //Converts to boolean
        if (array_key_exists('franceResident', $data)) {
            $object->setFranceResident((bool) $data['franceResident']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new Child();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'child-create', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

      
        $this->notificationService->create([
            "target_role" => "ROLE_ADMIN",
            "name"        => "Enfant",
            "description" => "Création d'un nouvel enfant : ".$object->getFullname(),
            "url"         => "/child/display/id/".$object->getChildId()."/"
        ]);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Enfant ajouté',
            'child' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Child $object)
    {
        //Removes links
        $this->removeLinks($object);
        $this->removeSiblings($object);

        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Enfant supprimé',
        );
    }

    public function searchSame($child) {
        $childs[] = $this->toArray($child);
        $others = $this->em->getRepository('App\Entity\Child')->findBy(['firstname' => $child->getFirstname(), 'lastname' => $child->getLastname(), 'suppressed' => 0]);
        foreach($others as $other) {
            if($other->getChildId() == $child->getChildId()) continue;
            $childs[] = $this->toArray($other);
        }

        return $childs;
    }


    public function doFusion($childIdToKeep, $childIdToDelete) {


        $childToKeep     =  $this->em->getRepository('App\Entity\Child')->find($childIdToKeep);
        $childToDelete   =  $this->em->getRepository('App\Entity\Child')->find($childIdToDelete);

        $messages = [];
        $message = [];

        $mode = "PROD";
       // $mode = "TEST";

        // fusion child information
        if( $childToKeep->getBirthdate() == "" OR  $childToKeep->getBirthdate() == null) {
            
            if($childToDelete->getBirthdate() != null OR $childToDelete->getBirthdate() != "") {
                $childToKeep->setBirthdate($childToDelete->getBirthdate());
                $message[] = "modification de la date de naissance";
            }
        }
    
        if( $childToKeep->getPhoto() == "" OR  $childToKeep->getPhoto() == null) {
            if($childToDelete->getPhoto() != null OR $childToDelete->getPhoto() != "") {
                $childToKeep->setPhoto($childToDelete->getPhoto());
                $message[] = "modification de la photo";
            }
        }
        if( $childToKeep->getGender() == "" OR  $childToKeep->getGender() == null) {

            if($childToDelete->getGender() != null OR $childToDelete->getGender() != "") {
                $childToKeep->setGender($childToDelete->getGender());
                $message[] = "modification du genre";
            }
        }
        
        if( $childToKeep->getPhone() == "" OR  $childToKeep->getPhone() == null) {
            if($childToDelete->getPhone() != null OR $childToDelete->getPhone() != "") {
                $childToKeep->setPhone($childToDelete->getPhone());
                $message[] = "modification du telephone";
            }
        }

        if( $childToKeep->getMedical() == "" OR  $childToKeep->getMedical() == null) {
            if($childToDelete->getMedical() != null OR $childToDelete->getMedical() != "") {
                $childToKeep->setMedical($childToDelete->getMedical());
                $message[] = "modification des informations médicales";
            }
        }

        if( $childToKeep->getFranceResident() == "" OR  $childToKeep->getFranceResident() == null) {
            if($childToDelete->getFranceResident() != null OR $childToDelete->getFranceResident() != "") {
                $childToKeep->setFranceResident($childToDelete->getFranceResident());
                $message[] = "modification de la résidence";
            }
        }
        
        if( $childToKeep->getPickupInstruction() == "" OR  $childToKeep->getPickupInstruction() == null) {
            if($childToDelete->getPickupInstruction() != null OR $childToDelete->getPickupInstruction() != "") {
                $childToKeep->setPickupInstruction($childToDelete->getPickupInstruction());
                $message[] = "modification des infos de prise en charge";
            }
        }

        if( $childToKeep->getComment() == "" OR  $childToKeep->getComment() == null) {
            if($childToDelete->getComment() != null OR $childToDelete->getComment() != "") {
                $childToKeep->setComment($childToDelete->getComment());
                $message[] = "modification du commentaire";
            }
        }

        if( $childToKeep->getSchool() == "" OR  $childToKeep->getSchool() == null) {
            if($childToDelete->getSchool() != null OR $childToDelete->getSchool() != "") {
                $childToKeep->setSchool($childToDelete->getSchool());
                $message[] = "modification de l'école";
            }
        }
        
        if( $childToKeep->getSportifProfil() == "" OR  $childToKeep->getSportifProfil() == null) {
            if($childToDelete->getSportifProfil() != null OR $childToDelete->getSportifProfil() != "") {
                $childToKeep->setSportifProfil($childToDelete->getSportifProfil());
                $message[] = "modification du profil sportif";
            }
        }

        if( $childToKeep->getStaff() == "" OR  $childToKeep->getStaff() == null) {
            if($childToDelete->getStaff() != null OR $childToDelete->getStaff() != "") {
                $childToKeep->setStaff($childToDelete->getStaff());
                $message[] = "modification du coach";
            }
        }
        
        if( $childToKeep->getChildHand() == "" OR  $childToKeep->getChildHand() == null) {
            if($childToDelete->getChildHand() != null OR $childToDelete->getChildHand() != "") {
                $childToKeep->setChildHand($childToDelete->getChildHand());
                $message[] = "modification de la main";
            }
        }

        if( $childToKeep->getChildFoot() == "" OR  $childToKeep->getChildFoot() == null) {
            if($childToDelete->getChildFoot() != null OR $childToDelete->getChildFoot() != "") {
                $childToKeep->setChildFoot($childToDelete->getChildFoot());
                $message[] = "modification du pied";
            }
        }
     
        if( $childToKeep->getGuidingEye() == "" OR  $childToKeep->getGuidingEye() == null) {
            if($childToDelete->getGuidingEye() != null OR $childToDelete->getGuidingEye() != "") {
                $childToKeep->setGuidingEye($childToDelete->getGuidingEye());
                $message[] = "modification de l'oeil";
            }
        }

        //Persists data
        if( $mode == "PROD") {
            $this->mainService->modify($childToKeep);
            $this->mainService->persist($childToKeep);
        }

        // tables to update
        $tables = [
            'booklet_child'         => 'livret',
            'child_child_link'      => 'enfant associé',
            'child_person_link'     => 'personnes associée',
            'child_presence'        => 'la présence',
            'historic_sms_list'     => 'les envois de SMS',
            'invoice'               => 'les factures',
            'meal'                  => 'les repas',
            'pickup'                => 'les transports',
            'pickup_activity'       => 'les actitvités',
            'registration'          => 'les inscriptions',
            'survey_session'        => 'les sondages',
            'media'                 => 'les photos'
        ];

        $tablesUpdated = []; $arr = [];
        foreach($tables as $table => $name) {
            $conn = $this->em->getConnection();

            $countQuery = "SELECT COUNT(*) as nb FROM ".$table." WHERE child_id = ".$childIdToDelete;

            $r = $conn->prepare($countQuery);
            $datas = $r->executeQuery()->fetchAssociative();
    
            $arr[$name] = $datas['nb'] ;

            if($datas['nb'] > 0 ) {
                $query = "UPDATE ".$table." SET child_id = ".$childIdToKeep. " WHERE child_id = ".$childIdToDelete; 
                $r = $conn->prepare($query);

                if( $mode == "PROD") {
                    $r->execute();
                }
                $tablesUpdated[] = $name;
            }
        }

        // delete child to delete
        if( $mode == "PROD") {
            $this->removeLinks($childToDelete);
            $this->removeSiblings($childToDelete);
    
            //Persists data
            $this->mainService->delete($childToDelete);
            $this->mainService->persist($childToDelete);
        }


        if(count($tablesUpdated) > 0 && count($message) > 0) {
            $status = "fusion";
        } else {
            $status = "no_fusion";
        }
        $messages['mode']              = $mode;
        $messages['status']            = $status; 
        $messages['tables_updated']    = $tablesUpdated;
        $messages['nb_tables_updated'] = $arr;
        $messages['child_updated']     = $message;

        return $messages;
    }

    /**
     * Returns the list of all children in the array format
     */
    public function findAll()
    {
        return $this->em
            ->getRepository('App\Entity\Child')
            ->findAll()
        ;
    }

    /**
     * Searches the term in the Child collection
     * @return array
     */
    public function findAllSearch(string $term)
    {
        return $this->em
            ->getRepository('App\Entity\Child')
            ->findAllSearch($term)
        ;
    }

    public function findFastSearch(string $term, $limit_age = 17) {

        $today = new \DateTime();
        $ageLimitDate = $today->modify('-' . $limit_age . ' years')->format('Y-m-d');


        $datas = [
                    ['name' => 'lastname', 'start' => ''],
                    ['name' => 'lastname', 'start' => '%'],
                    ['name' => 'firstname', 'start' => ''],
                    ['name' => 'firstname', 'start' => '%'],
        ];

        $result = null;

        $existsChild = [];

        foreach($datas as $data) {
            $childs = $this->em->getRepository('App\Entity\Child')
                ->createQueryBuilder('c')
                ->where('c.' . $data['name'] . ' LIKE :term')
                ->andWhere('c.birthdate >= :ageLimitDate')
                ->andWhere('c.suppressed = 0')
                ->setParameter('term', $data['start'] . $term . '%')
                ->setParameter('ageLimitDate', $ageLimitDate)
                ->getQuery()
                ->getResult();
            foreach($childs as $child) {
                $childId = $child->getChildId();
                if(!key_exists($childId, $existsChild)) {
                    $result[] = [   'id'       => $childId,
                                    'fullname' => strtoupper($child->getLastname()).' '.$child->getFirstname(),
                                    'photo'    => $child->getPhoto()
                                ];
                }
                $existsChild[$childId] = $childId;
            }
        }
     
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Child $object)
    {
        if (null === $object->getFirstname() ||
            null === $object->getLastname()) {
            throw new UnprocessableEntityHttpException('Missing data for Child -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Child $object, string $data)
    {

        //Submits data
        $data = $this->mainService->submit($object, 'child-modify', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Enfant modifié',
            'child' => $this->toArray($object),
        );
    }

    public function removeAllLinks(Child $object) {

    }

    public function addJustificatif($data): array
    {

        $datas = json_decode($data, true);
        foreach(explode(',', $datas['ids']) as $id) {
            $child = $this->em->getRepository('App\Entity\Child')->findOneById($id);
            if($child) {

                if ($datas['type'] == 'justificatif') {
                    $child->setFrontDocument($datas['url']);
                } else if ($datas['type'] == 'qrcode') {
                    $child->setFrontQr($datas['url']);
                }

                $this->mainService->modify($child);
                $this->mainService->persist($child);
            }
        }

        return ['message' => 'justificatif ajouté'];
    }

    public function removeDocument($data): array
    {

        $datas = json_decode($data, true);
        foreach(explode(',', $datas['ids']) as $id) {
            $child = $this->em->getRepository('App\Entity\Child')->findOneById($id);
            if($child) {

                if ($datas['type'] == 'justificatif') {
                    $child->setFrontDocument(null);
                } else if ($datas['type'] == 'qrcode') {
                    $child->setFrontQr(null);
                }

                $this->mainService->modify($child);
                $this->mainService->persist($child);
            }
        }

        return ['message' => 'document supprimé'];
    }

    public function removePerson($child, $personId) {
        foreach($child->getPersons( ) as $link) {
            if($link->getPerson()->getPersonId() == $personId) {
                $this->em->remove($link);
            }
        }
        $this->em->persist($child);
        $this->em->flush();
        return ['message' => 'liaison supprimée'];
    }

    /**
     * Removes links from person/s to child
     */
    public function removeLinks(Child $object)
    {
        if (!$object->getPersons()->isEmpty()) {
            foreach ($object->getPersons() as $link) {
                $this->em->remove($link);
            }
        }
    }

    /**
     * Removes links from child to child
     */
    public function removeSiblings(Child $object)
    {
        if (!$object->getSiblings()->isEmpty()) {
            foreach ($object->getSiblings() as $sibling) {
                $this->em->remove($sibling);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Child $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related school
        if (null !== $object->getSchool() && !$object->getSchool()->getSuppressed()) {
            $objectArray['school'] = $this->mainService->toArray($object->getSchool()->toArray());
        }

        //Gets related persons
        if (null !== $object->getPersons()) {
            $persons = array();
            foreach($object->getPersons() as $personLink) {
                if (!$personLink->getPerson()->getSuppressed()) {
                    $personArray = $this->personService->toArray($personLink->getPerson());
                    $personArray['relation'] = $personLink->getRelation();
                    $persons[] = $personArray;
                }
            }
            $objectArray['persons'] = $persons;
        }

        //Gets related siblings
        if (null !== $object->getSiblings()) {
            $siblings = array();
            foreach($object->getSiblings() as $siblingLink) {
                if (!$siblingLink->getSibling()->getSuppressed()) {
                    $siblingArray = $this->mainService->toArray($siblingLink->getSibling()->toArray());
                    $siblingArray['relation'] = $siblingLink->getRelation();
                    $siblings[] = $siblingArray;
                }
            }
            $objectArray['siblings'] = $siblings;
        }

        // get latest sport done
        if($latestRegistration = $this->em->getRepository('App\Entity\Registration')->findLatest($object)) {
            if (null !== $latestRegistration->getSports()) {
                $sports = array();
                foreach ($latestRegistration->getSports() as $sport) {
                    if (!$sport->getSport()->getSuppressed()) {
                        $sports[] = $this->mainService->toArray($sport->getSport()->toArray());
                    }
                }
                $objectArray['sports'] = $sports;
            }
            $objectArray['latestRegistrationId'] = $latestRegistration->getRegistrationId();

        }


        return $objectArray;
    }

    /**
     * retrieve birthdays staff of the current week
     */
    public function retrieveCurrentBirthdates()
    {

        $date_ref = date('Y-m-d');
        $n = 3; // nb days before and total of days = n*6
        $maxAge = 14;
        $start = date('Y-m-d', strtotime($date_ref.", -".$n." day"));
        $datesArray = array();
        $childs = $this->em->getRepository('App\Entity\Child')->retrieveCurrentBirthdates($start, $n*2, $maxAge);

        if($childs) {
                    foreach($childs as $child) {
                        $datesArray[$child->getBirthdate()->format('m-d')][] = [
                                        'full_name' => $child->getFirstname().' '.$child->getLastname(),
                                        'birthdate' => $child->getBirthdate()->format('Y-m-d')
                                    ];
                        }
        } else {
            $datesArray = ['message' => "aucun enfant n'est née dans cette période"];
        }

        return $datesArray;
    }
}
