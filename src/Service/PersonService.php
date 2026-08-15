<?php

namespace App\Service;

use App\Entity\ChildPersonLink;
use App\Entity\Person;
use App\Entity\PersonAddressLink;
use App\Entity\PersonPersonLink;
use App\Entity\PersonPhoneLink;
use App\Entity\UserPersonLink;
use App\Entity\Phone;
use App\Entity\Child;
use App\Service\PhoneService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * PersonService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PersonService implements PersonServiceInterface
{
    private $addressService;

    private $staffService;

    private $notificationService;

    private $em;

    private $mainService;

    public function __construct(
        AddressServiceInterface $addressService,
        StaffServiceInterface $staffService,
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        PhoneService $phoneService,
        NotificationService $notificationService

    )
    {
        $this->addressService = $addressService;
        $this->staffService = $staffService;
        $this->em = $em;
        $this->mainService = $mainService;
        $this->phoneService = $phoneService;
        $this->notificationService = $notificationService;


    }

    /**
     * Adds relation between Person and Person
     */
    public function addRelation(int $relationId, string $relation, Person $object)
    {
        $related = $this->em->getRepository('App\Entity\Person')->findOneByPersonId($relationId);
        if ($related instanceof Person && $object !== $related) {
            $personPersonLink = new PersonPersonLink();
            $personPersonLink
                ->setPerson($object)
                ->setRelated($related)
                ->setRelation($relation)
            ;
            $this->em->persist($personPersonLink);
        //Bad PersonId
        } else {
            throw new UnprocessableEntityHttpException('Submitted related Person with PersonId: "' . $relationId.'" cannot be added as relation to Person with PersonId: "' . $object->getPersonId() . '"');
        }
    }

    /**
     * Adds specific data that could not be added via generic method
     */
    public function addSpecificData(Person $object, array $data)
    {
        //Adds relations
        if (array_key_exists('relations', $data)) {
            foreach ($data['relations'] as $relation) {
                $this->addRelation($relation['related'], $relation['relation'], $object);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new Person();
        $data = json_decode($data, true);
        $user = array_key_exists('identifier', $data) ? $this->em->getRepository('App\Entity\User')->findOneByIdentifier($data['identifier']) : null;
        $this->mainService->create($object, $user);
        $data = $this->mainService->submit($object, 'person-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Adds links from user to person
        if (null !== $user) {
            $userPersonLink = new UserPersonLink();
            $userPersonLink
                ->setUser($user)
                ->setPerson($object)
            ;
            $this->em->persist($userPersonLink);
        }

        //Persists data
        $this->mainService->persist($object);

        //Adds relations that must be added after the creation of the Person
        $this->em->refresh($object);
        $this->addSpecificData($object, $data);
        $this->em->flush();


        $this->notificationService->create([
            "target_role" => "ROLE_ADMIN",
            "name"        => "Personne",
            "description" => "Création d'une nouvelle personne : ".$object->getFullname(),
            "url"         => "/person/display/id/".$object->getPersonId()."/"
        ]);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Personne ajoutée',
            'person' => $this->toArray($object),
        );
    }


    public function associate($personId, $childId) {
        $person = $this->em->getRepository('App\Entity\Person')->find($personId);
        $child  = $this->em->getRepository('App\Entity\Child')->find($childId);

        $childPersonLink = new ChildPersonLink();
        $childPersonLink
            ->setRelation("Parent")
            ->setChild($child)
            ->setPerson($person)
        ;
        $this->em->persist($childPersonLink);
        $this->em->flush();

        return [ 
                    'ChildPersonLinkId' => $childPersonLink->getChildPersonLinkId(),
                    'personId' => $person->getPersonId(),
                    'childId'  => $child->getChildId() 
                ];
    }

    /**
     * Retrive a perosn by pone number
     *
     * @param string $number
     * @return array
     */
    public function getPersonFromNumber($number)
    {
        $pattern = '/^(\+33|33|0)/';
        $number = preg_replace($pattern, '', $number);

        $phones = $this->em->getRepository('App\Entity\Phone')->findLike($number);
        foreach($phones as $phone) {
            //$result[] = $this->phoneService->toArray($phone);

            foreach($phone->getPersons() as $personLink)
            {
                $person = $personLink->getPerson();
                $result[$person->getFirstname().' '.$person->getLastname()] = $person->getFirstname().' '.$person->getLastname();
            }
        }

        foreach($result as $r) {
            $resultArra[] = $r;
        }

        return array($resultArra);
    }

    public function unassociate($personId1, $personId2) {

        if(!$person1 = $this->em->getRepository('App\Entity\Person')->find($personId1)) return ['message' => 'person1 not founded'];
        foreach($person1->getRelated() as $link) {
            $currentRelatedId = $link->getPerson()->getPersonId();

            $arr[] = $currentRelatedId;

            if($personId2 == $currentRelatedId) {

                $this->em->remove($link);
                $this->em->flush();
            }
        }

        return ['message' => 'person-unassociate-personId-'.$personId2];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Person $object)
    {
        //Removes links from person to child
        $childPersonLinks = $this->em->getRepository('App\Entity\ChildPersonLink')->findByPerson($object);
        foreach ($childPersonLinks as $childPersonLink) {
            if ($childPersonLink instanceof ChildPersonLink) {
                $this->em->remove($childPersonLink);
            }
        }

        //Removes links from person to address
        $objectAddressLinks = $this->em->getRepository('App\Entity\PersonAddressLink')->findByPerson($object);
        foreach ($objectAddressLinks as $objectAddressLink) {
            if ($objectAddressLink instanceof PersonAddressLink) {
                $this->em->remove($objectAddressLink);
            }
        }

        //Removes links from person to phone
        $objectPhoneLinks = $this->em->getRepository('App\Entity\PersonPhoneLink')->findByPerson($object);
        foreach ($objectPhoneLinks as $objectPhoneLink) {
            if ($objectPhoneLink instanceof PersonPhoneLink) {
                $this->em->remove($objectPhoneLink);
            }
        }

        //Removes links from person to person
        $personPersonLinks = $this->em->getRepository('App\Entity\PersonPersonLink')->findByPerson($object);
        $relatedPersonLinks = $this->em->getRepository('App\Entity\PersonPersonLink')->findByRelated($object);
        foreach (array_merge($personPersonLinks, $relatedPersonLinks) as $personPersonLink) {
            if ($personPersonLink instanceof personPersonLink) {
                $this->em->remove($personPersonLink);
            }
        }

        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        //Removes links from user to person (has to be there otherwise its re-created via cascade persist)
        $userPersonLink = $this->em->getRepository('App\Entity\UserPersonLink')->findOneByPerson($object);
        if ($userPersonLink instanceof UserPersonLink) {
            $this->em->remove($userPersonLink);
            $this->em->flush();
        }

        return array(
            'status' => true,
            'message' => 'Personne supprimée',
        );
    }

    /**
     * Returns the list of all persons in the array format
     * @return array
     */
    public function findAll()
    {
        return $this->em
            ->getRepository('App\Entity\Person')
            ->findBy(['suppressed' => 0], ['personId' => 'DESC'])
        ;
    }

    /**
     * Searches the term in the Person collection
     * 
     * DEPRECATEAD
     * 
     * @return array
     */
    public function findAllSearch(string $term)
    {
        return $this->em
            ->getRepository('App\Entity\Person')
            ->findAllSearch($term)
        ;
    }


    /**
     * Searches the term in the Person collection
     * 
     * DEPRECATEAD
     * 
     * @return array
     */
    public function searchByTerm(string $term)
    {
        $results1 = $this->em
            ->getRepository('App\Entity\Person')
            ->findLastnameByTerm($term.'%')
        ;

         $results2 = $this->em
            ->getRepository('App\Entity\Person')
            ->findLastnameByTerm($term.'%', 'firstname')
        ;

        $results3 = $this->em
            ->getRepository('App\Entity\Person')
            ->findLastnameByTerm('%'.$term.'%')
        ;

        $results4 = $this->em
            ->getRepository('App\Entity\Person')
            ->findLastnameByTerm('%'.$term.'%', 'firstname')
        ;

        $results = array_merge($results1, $results2, $results3, $results4);

        return $results;
    }

    public function searchByCriteria($datas) {
        $data = json_decode($datas, true);

        // number
            $number = $data['number'];
            $pattern = '/^(\+33|33|0)/';
            $number = preg_replace($pattern, '', $number);

            $rsm = new ResultSetMapping();

            $rsm->addEntityResult(Person::class, 'p');
            $rsm->addFieldResult('p', 'person_id', 'personId');
            $rsm->addFieldResult('p', 'firstname', 'firstname');
            $rsm->addFieldResult('p', 'lastname', 'lastname');

            $sql = "SELECT p.firstname, p.person_id, p.lastname  FROM person p
            INNER JOIN person_phone_link lk ON lk.person_id = p.person_id
            INNER JOIN phone ph ON ph.phone_id = lk.phone_id
            LEFT JOIN child_person_link cpl ON cpl.person_id = p.person_id
            LEFT JOIN child c ON c.child_id = cpl.child_id
            WHERE 1 = 1  ";

            if(isset($data['number'])) $sql .= " AND ph.phone LIKE :number";
            if(isset($data['name'])) $sql .= " AND p.lastname LIKE :name";
            if(isset($data['childname'])) $sql .= " AND c.lastname LIKE :childname";

            $nativeQuery = $this->em->createNativeQuery($sql, $rsm);

            if (isset($data['number'])) { $nativeQuery->setParameter('number', '%' . $number . '%');}
            if (isset($data['name'])) { $nativeQuery->setParameter('name', '%' . $data['name'] . '%');}
            if (isset($data['childname'])) { $nativeQuery->setParameter('childname', '%' . $data['childname'] . '%');}

            $persons = $nativeQuery->getResult();

            foreach($persons as $person) {
                $tels = [];
                foreach($person->getPhones() as $link) {
                    $tels[$link->getPhone()->getPhone()] = $link->getPhone()->getPhone();
                }



                $result[$person->getPersonId()]['person'] = ['id' => $person->toArray()['personId'], 'firstname' => $person->toArray()['firstname'], 'lastname' => $person->toArray()['lastname']];
                $result[$person->getPersonId()]['phone'] = $tels;

                $children = $person->getChildren();
                $allchilds = [];
                foreach ($children as $link) {
                    $child = $link->getChild();
                    $allchilds[] = [
                        'id' => $child->getChildId(),
                        'firstname' => $child->getFirstname(),
                        'lastname' => $child->getLastname(),
                    ];
                }


                $result[$person->getPersonId()]['child'] = $allchilds;
            }

        return $result;


    }


    /**
     * Returns the Person using its user's identifier
     * @return array
     */
    public function findByUserIdentifier($identifier)
    {
        return $this->em
            ->getRepository('App\Entity\Person')
            ->findByUserIdentifier($identifier)
        ;
    }

       /**
     * Returns the Person using its user's identifier
     * @return array
     */
    public function findByUserId($userId)
    {
        return $this->em
            ->getRepository('App\Entity\Person')
            ->findByUserId($userId)
        ;
    }

    /**
     * Finds one with its id
     * @return array
     */
    public function findOneById(int $personId)
    {
        return $this->em
            ->getRepository('App\Entity\Person')
            ->findOneById($personId)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Person $object)
    {
        if (null === $object->getFirstname() ||
            null === $object->getLastname()) {
            throw new UnprocessableEntityHttpException('Missing data for Person -> ' . json_encode($object->toArray()));
        }
    }

    public function updateBlackListed($person, $value) {
        $person->setIsBlackListed($value);
        $this->em->persist($person);
        $this->em->flush();

        return $person->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Person $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'person-modify', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Personne modifiée',
            'person' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Person $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets User's data
        if (null !== $object->getUserPersonLink() && null !== $object->getUserPersonLink()->getUser()) {
            $user = $object->getUserPersonLink()->getUser();
            $objectArray['email'] = $user->getEmail();
            $objectArray['identifier'] = $user->getIdentifier();
            $objectArray['user_id'] = $user->getId();

            if($user->getDevices()) {
                foreach($user->getDevices() as $device)
                {
                    $deviceArray = $device->toArray();
                    if(isset($deviceArray['user'])) unset($deviceArray['user']);
                    $objectArray['devices'][] = $deviceArray;

                }
            }




            unset($objectArray['userPersonLink']);
        }

        //Gets staff if is one
        $staff = $this->em->getRepository('App\Entity\Staff')->findOneByPerson($object->getPersonId());
        // deprecated
        $objectArray['driver'] = null !== $staff ? $this->staffService->toArray($staff) : null;
        $objectArray['staff'] = $objectArray['driver'];
        $objectArray['driver']['disclaimer'] = 'use Staff this array is depreacated';



        //Gets related addresses
        if (null !== $object->getAddresses()) {
            $addresses = array();
            foreach($object->getAddresses() as $addressLink) {
                if(!$addressLink->getAddress()) {
                    $addresses[] = "address not found for ".$addressLink->getAddressId();
                } else {
                    if (!$addressLink->getAddress()->getSuppressed()) {
                        $addresses[] = $this->mainService->toArray($addressLink->getAddress()->toArray());
                    }
                }

            }
            $objectArray['addresses'] = $addresses;
        }

        //Gets related phones
        if (null !== $object->getPhones()) {
            $phones = array();
            foreach($object->getPhones() as $phoneLink) {
                if (!$phoneLink->getPhone()->getSuppressed()) {
                    $phones[] = $this->mainService->toArray($phoneLink->getPhone()->toArray());
                }
            }
            $objectArray['phones'] = $phones;
        }

        //Gets related children
        if (null !== $object->getChildren()) {
            $children = array();
            foreach($object->getChildren() as $childLink) {
                if (!$childLink->getChild()->getSuppressed()) {
                    $children[] = $this->mainService->toArray($childLink->getChild()->toArray());
                }
            }
            $objectArray['children'] = $children;
        }

        //Gets relations persons
        if (null !== $object->getRelations()) {
            $relations = array();
            foreach($object->getRelations() as $relationLink) {
                if (!$relationLink->getRelated()->getSuppressed()) {
                    $relationArray = $this->toArray($relationLink->getRelated());
                    $relationArray['relation'] = $relationLink->getRelation();
                    $relations[] = $relationArray;
                }
            }
            $objectArray['relations'] = $relations;
        }

        //Gets related persons
        if (null !== $object->getRelated()) {
            $related = array();
            foreach($object->getRelated() as $relatedLink) {
                if (!$relatedLink->getPerson()->getSuppressed()) {
                    $relatedArray = $relatedLink->getPerson()->toArray();
                    $relatedArray['related'] = $relatedLink->getRelation();
                    $related[] = $relatedArray;
                }
            }
            $objectArray['related'] = $related;
        }

        return $objectArray;
    }

    public function listDoublon() {


        $letter = 'A';

        $persons = $this->em->getRepository('App\Entity\Person')->findDoublon($letter);

        $uniqueLastname = [];
        $doublonLastname = [];

        foreach($persons as $person) {
            $keyLastname = $person['lastname'];

            // vérifie si existe dans uniquelastname


                if(in_array($keyLastname, $uniqueLastname)) {
                    // si oui 

                    // recupère clé de uniqueLastname

                    // ajoute uniquelastname dans doublon

                    // ajoute nouveau dans doublon
                } else {

                }
                

                // si non ajout à uniqueLastname




        }

        // 2ème boucle dans unqiueLastname pour vérifier firstname

        return $persons;
    }

    public function searchByEmail(string $email)
    {
        return $this->em
            ->getRepository('App\Entity\Person')
            ->findByEmail($email)
        ;
    }

    public function freeEmail(int $userId)
    {
        $user = $this->em->getRepository('App\Entity\User')->find($userId);

        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $currentEmail = $user->getEmail();

        if (strpos($currentEmail, '_old') !== false) {
            return ['success' => false, 'message' => 'Email already freed'];
        }

        $user->setEmail($currentEmail . '_old');
        $this->em->flush();

        return ['success' => true, 'message' => 'Email freed', 'old_email' => $currentEmail, 'new_email' => $currentEmail . '_old'];
    }
}
