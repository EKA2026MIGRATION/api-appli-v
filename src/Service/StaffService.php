<?php

namespace App\Service;

use App\Entity\DriverZone;
use App\Entity\Staff;
use App\Entity\User; 

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * StaffService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class StaffService implements StaffServiceInterface
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
    public function addZone(Staff $object, string $postal, int $priority)
    {
        $driverZone = new DriverZone();
        $driverZone
            ->setStaff($object)
            ->setPostal($postal)
            ->setPriority($priority)
        ;

        $this->mainService->create($driverZone);
        $this->mainService->persist($driverZone);

        return $driverZone;
    }

    public function addDriverZone($staff, $data) {
        $data = json_decode($data, true);
        $driverZone = $this->addZone($staff, $data['postal'], 1);
        return $driverZone->toArray();
    }

    public function updateZone($driverZoneId, $priority) {
        $driverZone = $this->em->getRepository('App\Entity\DriverZone')->find($driverZoneId);
        $driverZone->setPriority($priority);
        $this->em->persist($driverZone);
        $this->em->flush();

        return $driverZone->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findSupervisor()
    {
        $staffs = $this->em->getRepository('App\Entity\Staff')->findBy(array('isSupervisor' => 1));
        return $staffs;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {

        // test if exist
        $dataTest = json_decode($data, true);
        $person = $this->em->getRepository('App\Entity\Person')->find($dataTest['person']);
        if($staff = $this->em->getRepository('App\Entity\Staff')->findOneBy(['person' => $person])) {
            $staff->setSuppressed(0);
            $staff->setSuppressedAt(null);
            $staff->setMaxChildren($dataTest['maxChildren']);
            $this->em->persist($staff);
            $this->em->flush();
    
            return array(
                'status' => true,
                'message' => 'Staff réactivé',
                'staff' => $this->toArray($staff),
            ); 
        }


        //Submits data
        $object = new Staff();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'staff-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Adds links for DriverZones
        if (array_key_exists('links', $data)) {
            $links = $data['links'];
            if (null !== $links && is_array($links) && !empty($links)) {
                foreach ($links as $link) {
                    $this->addZone($object, $link['postal'], $link['priority']);
                }
            }
        }

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Staff ajouté',
            'staff' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Staff $object)
    {
        //Removes links for zones
        if (!$object->getDriverZones()->isEmpty()) {
            foreach ($object->getDriverZones() as $link) {
                $this->em->remove($link);
            }
        }

        //Removes links for groupActivity
        if (!$object->getGroupActivities()->isEmpty()) {
            foreach ($object->getGroupActivities() as $link) {
                $this->em->remove($link);
            }
        }

        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Staff supprimé',
        );
    }

    /**
     * Returns the Staff based on its id
     * @return Staff
     */
    public function findOneById(int $staffId)
    {
        return $this->em
            ->getRepository('App\Entity\Staff')
            ->findOneById($staffId)
        ;
    }

    /**
     * Returns the maxmium number of DriverZones
     * @return int
     */
    public function getMaxDriverZones()
    {
        return $this->em
            ->getRepository('App\Entity\DriverZone')
            ->getMaxDriverZones()
        ;
    }

    /**
     * Returns the list of all staffs in the array format
     * @return array
     */
    public function findAllByKind(string $kind, $isActive = 1)
    {
        return $this->em
            ->getRepository('App\Entity\Staff')
            ->findAllByKind($kind, $isActive)
        ;
    }

    /**
     * Returns the list of all staffs in the array format
     * @return array
     */
    public function findAllSearch(string $term, $isActive = 1)
    {
        return $this->em
            ->getRepository('App\Entity\Staff')
            ->findAllSearch($term, $isActive)
        ;
    }

    /**
     * Returns the list of all staffs in the array format
     * @return array
     */
    public function findUserNoStaffYet(string $term)
    {
        $repository = $this->em->getRepository(User::Class);
        $users = $repository->findUserNoStaffYet($term);

        $userArray = array();
        foreach ($users as $user) {
            $userA = $user->toArray();
            if($user->getUserPersonLink()->getPerson()) $userA['person'] = $user->getUserPersonLink()->getPerson()->toArray();
            $userArray[] = $userA;

        };

        return $userArray;

    }



    /**
     * Returns the list of all staffs in the array format
     * @return array
     */
    public function findStaffUser()
    {
        $repository = $this->em->getRepository(User::Class);
        $users = $repository->findUserStaff();

        return ['hello'];

        $userArray = array();
        foreach ($users as $user) {
            $userA = $user->toArray();
            if($user->getUserPersonLink()->getPerson()) $userA['person'] = $user->getUserPersonLink()->getPerson()->toArray();
            $userArray[] = $userA;

        };

        return $userArray;

    }


    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Staff $object)
    {
        if (null === $object->getPerson()) {
            throw new UnprocessableEntityHttpException('Missing data for Staff -> ' . json_encode($object->toArray()));
        }
    }


    public function updateIsActive(Staff $staff, $data) {

        $data = json_decode($data, true);

        $staff->setIsActive($data['isActive']);
        
        //Persists data
        $this->mainService->modify($staff);
        $this->mainService->persist($staff);


        // update enabled user
        $user = $staff->getPerson()->getUserPersonLink()->getUser();
        $user->setEnabled($staff->getIsActive());

        $this->em->persist($user);
        $this->em->flush();


 
         //Returns data
         return array(
             'status' => true,
             'message' => 'Staff isActive modifié',
             'staff' => $this->toArray($staff),
         );

    }

    /**
     * {@inheritdoc}
     */
    public function modify(Staff $object, string $data)
    {

        //Submits data
        $data = $this->mainService->submit($object, 'staff-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Modifies links for driverZones
        if (array_key_exists('links', $data)) {
            $links = $data['links'];

            if (null !== $links && is_array($links) && !empty($links)) {
                //Removes existing zones
                foreach ($object->getDriverZones() as $driverZone) {
                    $this->em->remove($driverZone);
                }

                //Adds submitted zones
                foreach ($links as $link) {
                    $this->addZone($object, $link['postal'], $link['priority']);
                }
            }
        }

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Staff modifié',
            'staff' => $this->toArray($object),
        );
    }

    /**
     * Modifies the priorities for Staffs
     */
    public function priority(string $data)
    {
        //Modifies priorities
        $data = json_decode($data, true);
        if (is_array($data) && !empty($data)) {
            foreach ($data as $staffPriority) {
                $staff = $this->em->getRepository('App\Entity\Staff')->findOneById($staffPriority['staff']);
                if ($staff instanceof Staff && !$staff->getSuppressed()) {
                    $staff->setPriority($staffPriority['priority']);
                    $this->mainService->modify($staff);
                    $this->mainService->persist($staff);
                }
            }

            //Returns data
            return array(
                'status' => true,
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . json_encode($data));
    }

    /**
     * retrieve birthdays staff of the current week
     */
    public function retrieveCurrentBirthdates()
    {

        $date_ref = date('Y-m-d');
        $n = 5;
        $start = date('Y-m-d', strtotime($date_ref.", -".$n." day"));
        $datesArray = array();
        $staffs = $this->em->getRepository('App\Entity\Staff')->retrieveCurrentBirthdates($start, $n*2);
        if($staffs) {
            foreach($staffs as $staff) {
                $person = $staff->getPerson();
                $datesArray[$person->getBirthdate()->format('m-d')][]  = [
                                                'full_name' => $person->getFirstname().' '.$staff->getPerson()->getLastname(),
                                                'birthdate' => $person->getBirthdate()->format('Y-m-d'),
                                                'kind'      => $staff->getKind()
                                            ];
            }
        } else {
            $datesArray = ['message' => "aucune personne n'est née dans cette période"];
        }


        return $datesArray;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Staff $object, $latestMeal = null)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        $objectArray['latestMeal'] = [];

        //Gets related person
        if (null !== $object->getPerson() && !$object->getPerson()->getSuppressed()) {
            $objectArray['person'] = $this->mainService->toArray($object->getPerson()->toArray());

            if($latestMeal) {
                if($meal = $this->em->getRepository('App\Entity\Meal')->findOneBy(['person' => $object->getPerson()], ['date' => 'DESC'])) {
                    $objectArray['latestMeal'] = $meal->toArray();


                      // get related food
                        if($foodObjects = $this->em->getRepository('App\Entity\Food')->findByMeal($meal)) {
                            foreach($foodObjects as $foodObject) {
                                $objectArray['latestMeal']['allfoods'][] = $foodObject->toArray();
                            }
                        } else {
                            $objectArray['latestMeal']['allfoods'] = [];  
                        }
                        
                        //Gets related foods
                        if (null !== $meal->getFoods()) {
                            $foods = array();
                            foreach($meal->getFoods() as $foodLink) {
                                if (!$foodLink->getFood()->getSuppressed()) {
                                $foods[] = $this->mainService->toArray($foodLink->getFood()->toArray());
                                }
                            }
                            $objectArray['latestMeal']['foods'] = $foods;
                        }

                }
            }
        }

        //Gets related vehicle
        if (null !== $object->getVehicle() && !$object->getVehicle()->getSuppressed()) {
            $objectArray['vehicle'] = $this->mainService->toArray($object->getVehicle()->toArray());
        }

        //Gets related address
        if (null !== $object->getAddress() && !$object->getAddress()->getSuppressed()) {
            $objectArray['address'] = $this->mainService->toArray($object->getAddress()->toArray());
        }

        //Gets related driverZones
        if (null !== $object->getDriverZones()) {
            $driverZones = array();
            foreach($object->getDriverZones() as $driverZone) {
                if (!$driverZone->getSuppressed()) {
                    $driverZones[] = $this->mainService->toArray($driverZone->toArray());
                }
            }
            $objectArray['driverZones'] = $driverZones;
        }

        return $objectArray;
    }
}
