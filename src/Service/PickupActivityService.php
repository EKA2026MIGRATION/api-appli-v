<?php

namespace App\Service;

use App\Entity\GroupActivity;
use App\Entity\Location;
use App\Entity\PickupActivity;
use App\Entity\PickupActivityGroupActivityLink;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Entity\ChildPresence;

/**
 * PickupActivityService class.
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupActivityService implements PickupActivityServiceInterface
{
    private $em;

    private $groupActivities = array();

    private $mainService;
    private $childPresenceService;

    public function __construct(EntityManagerInterface $em,MainServiceInterface $mainService) {
        $this->em = $em;
        $this->mainService = $mainService;
       // $this->childPresenceService = $childPresenceService;
    }

    /**
     * Adds link between PickupActivity and GroupActivity.
     */
    public function addLink(int $groupActivityId, PickupActivity $object)
    {
        $groupActivity = $this->em->getRepository('App\Entity\GroupActivity')->findOneById($groupActivityId);
        if ($groupActivity instanceof GroupActivity && !$groupActivity->getSuppressed()) {
            $pickupActivityGroupActivityLink = new PickupActivityGroupActivityLink();
            $pickupActivityGroupActivityLink
                ->setPickupActivity($object)
                ->setGroupActivity($groupActivity)
            ;
            $this->em->persist($pickupActivityGroupActivityLink);
        }
    }

    /**
     * Adds specific data that could not be added via generic method.
     */
    public function addSpecificData(PickupActivity $object, array $data)
    {
        //Should be done from GroupActivityType but it returns null...
        if (array_key_exists('start', $data)) {
            $object->setStart(DateTime::createFromFormat('H:i:s', $data['start']));
        }
        if (array_key_exists('end', $data)) {
            $object->setEnd(DateTime::createFromFormat('H:i:s', $data['end']));
        }

        //Adds links from PickupActivity to GroupActivity
        if (array_key_exists('links', $data)) {
            //Deletes old links
            $oldLinks = $object->getGroupActivities();
            if (null !== $oldLinks && !empty($oldLinks)) {
                foreach ($oldLinks as $oldLink) {
                    $this->em->remove($oldLink);
                }
            }

            //Adds new links
            $links = $data['links'];
            if (null !== $links && is_array($links) && !empty($links)) {
                foreach ($links as $link) {
                    $this->addLink((int) $link['groupActivityId'], $object);
                }
            }
        }
    }

    /**
     * Affects the PickupsActivity to GroupActivity for a specific date.
     */
    public function affect($date, bool $force)
    {
        //Unaffects PickupsActivity if force is requested
        if ($force) {
            $this->unaffect($date);
        }

        //Defines PickupsActivity to use
        $pickupActivities = $this->getPickupActivities($date);

        //Creates GroupActivities and affects PickupActivity to them
        if (!empty($pickupActivities)) {
            $parameters = $this->getParameters();

            // lunch list child
            $lunchIdList = [];

            //Affects PickupActivity to GroupActivity
            foreach ($pickupActivities as $sport => $pickupActivitiesGroup) {
                $sportId = (int) str_replace('sport-', '', $sport);

                foreach ($pickupActivitiesGroup as $ageGroup => $pickupActivities) {
                    $maxGroupAge = false !== strpos($ageGroup, '+') ? $parameters['maxGroupAgeOverMax'] : $parameters['maxGroupAgeUnderMax'];
                    foreach ($pickupActivities as $pickupActivity) {
                        //Defines data related to PickupActivity
                        $start = $pickupActivity->getStart();
                        $end = $pickupActivity->getEnd();

                        //If PickupActivity covers whole day, defines the number of GroupActivity
                        $groupActivityNumber = $start <= $parameters['groupActivityMorningStart'] && $end >= $parameters['groupActivityAfternoonEnd'] ? $parameters['totalGroupActivity'] : 1;

                        //Affects the PickupActivity to GroupActivity
                        for ($i = 0; $i < $groupActivityNumber; ++$i) {
                            //Use the start/end of PickupActivity if for half-day otherwise (whole day) use the start/end by default
                            $start = 1 === $groupActivityNumber || (2 === $groupActivityNumber && 0 === $i) ? $pickupActivity->getStart() : $parameters['groupActivityAfternoonStart'];
                            $end = 1 === $groupActivityNumber || (2 === $groupActivityNumber && 1 === $i) ? $pickupActivity->getEnd() : $parameters['groupActivityMorningEnd'];
                            $groupActivityStart = null;

                            // lunch exception
                            if ($sportId == 10) {
                                $groupActivityStart = $parameters['groupActivityLunchStart'];
                                $groupActivityEnd = $parameters['groupActivityLunchEnd'];
                            } else {
                                //Morning group
                                if (($start <= $parameters['groupActivityMorningStart'] && $end >= $parameters['groupActivityMorningEnd']) && $i == 0) {
                                    $groupActivityStart = $parameters['groupActivityMorningStart'];
                                    $groupActivityEnd = $parameters['groupActivityMorningEnd'];
                                //Afternoon group
                                } elseif ($start <= $parameters['groupActivityAfternoonStart'] && $end >= $parameters['groupActivityAfternoonEnd']) {
                                    $groupActivityStart = $parameters['groupActivityAfternoonStart'];
                                    $groupActivityEnd = $parameters['groupActivityAfternoonEnd'];
                                }
                            }

                            //Checks if the PickupActivity can be linked to a group
                            if (null !== $groupActivityStart) {
                                //Defines data used to create GroupActivity
                                $location = $pickupActivity->getLocation();
                                if (!$location instanceof Location) {
                                    $location = $parameters['groupActivityDefaultLocation'];
                                }
                                $dataGroupActivity = array(
                                    'date' => $date,
                                    'ageGroup' => $ageGroup,
                                    'start' => $groupActivityStart,
                                    'end' => $groupActivityEnd,
                                    'sportId' => $sportId,
                                    'location' => $location,
                                );

                                // add child to lunch list
                                if ($sportId == '10' && in_array($pickupActivity->getChild()->getChildId(), $lunchIdList)) {
                                } else {
                                    if ($sportId == 10) {
                                        $lunchIdList[] = $pickupActivity->getChild()->getChildId();
                                    }

                                    //Uses existing GroupActivity
                                    $key = $location->getLocationId().'-'.$groupActivityStart->format('Hi').'-'.$sportId.'-'.$ageGroup;
                                    $groupActivities = array_key_exists($key, $this->groupActivities) ? $this->groupActivities[$key] : null;
                                    if (is_array($groupActivities)) {
                                        $this->affectToGroupActivities($groupActivities, $pickupActivity, $maxGroupAge, $dataGroupActivity);
                                    //Creates GroupActivity if none has been found
                                    } else {
                                        $groupActivity = $this->createGroupActivity($dataGroupActivity);
                                        $this->affectToGroupActivity($pickupActivity, $groupActivity);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Affects PickupActivity to GroupActivity.
     */
    public function affectToGroupActivities($groupActivities, $pickupActivity, $maxGroupAge, $dataGroupActivity)
    {
        $affect = false;
        foreach ($groupActivities as $groupActivity) {
            //Affects PickupActivity to GroupActivity if not full
            if ($groupActivity->getPickupActivities()->count() < $maxGroupAge) {
                $this->affectToGroupActivity($pickupActivity, $groupActivity);
                $affect = true;
                break;
            }
        }

        //Creates GroupActivity if all are full
        if (!$affect) {
            $groupActivity = $this->createGroupActivity($dataGroupActivity);
            $this->affectToGroupActivity($pickupActivity, $groupActivity);
        }
    }


    public function addActivityToGroup($pickupActivityId, $groupdId) {
        
        $activity       = $this->em->getRepository('App\Entity\PickupActivity')->find($pickupActivityId);
        $groupActivity = $this->em->getRepository('App\Entity\GroupActivity')->find($groupdId);

        $this->affectToGroupActivity($activity, $groupActivity);

        return ['messages' => 'pickupAdded'];


    }

    /**
     * Affects PickupActivity to GroupActivity.
     */
    public function affectToGroupActivity($pickupActivity, $groupActivity)
    {
        $pickupActivityGroupActivityLink = new PickupActivityGroupActivityLink();
        $pickupActivityGroupActivityLink
            ->setPickupActivity($pickupActivity)
            ->setGroupActivity($groupActivity)
        ;

        //Persists data
        $this->em->persist($pickupActivityGroupActivityLink);
        $this->em->flush();
        $this->em->refresh($groupActivity);
        $this->em->refresh($pickupActivity);
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data, $return = true)
    {
        //Submits data
        $object = new PickupActivity();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'pickup-activity-create', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        if ($return) {
            return array(
                'status' => true,
                'message' => 'PickupActivity ajouté',
                'pickupActivity' => $this->toArray($object),
            );
        }
    }

    public function updateAllRegistration(string $data) {
        $dataArray = is_array($data) ? $data : json_decode($data, true);

        $sport = $this->em->getRepository('App\Entity\Sport')->findOneById($dataArray['sportId']);

        foreach($dataArray['pickupActivityIds'] as $pickupActivityId) {
            $activity = $this->em->getRepository('App\Entity\PickupActivity')->find($pickupActivityId);
            $activity->setSport($sport);
            $this->mainService->persist($activity);
        }
    }

    /**
     * Creates the GroupActivity.
     */
    public function createGroupActivity($data)
    {
        //Creates GroupActivity
        $groupActivity = new GroupActivity();
        $this->mainService->create($groupActivity);
        $groupActivity
            ->setDate(new DateTime($data['date']))
            ->setAge($data['ageGroup'])
            ->setStart($data['start'])
            ->setEnd($data['end'])
            ->setLocation($data['location'])
            ->setSport($this->em->getRepository('App\Entity\Sport')->findOneById($data['sportId']))
        ;
        $this->mainService->persist($groupActivity);
        $this->groupActivities[$data['location']->getLocationId().'-'.$data['start']->format('Hi').'-'.$data['sportId'].'-'.$data['ageGroup']][] = $groupActivity;

        return $groupActivity;
    }

    /**
     * Creates multiples PickupActivities.
     */
    public function createMultiple(string $data)
    {
        $data = json_decode($data, true);
        if (is_array($data) && !empty($data)) {
            foreach ($data as $newPickupActivity) {
                $this->create(json_encode($newPickupActivity), false);
            }

            //Returns data
            return array(
                'status' => true,
                'message' => 'PickupActivities ajoutés',
            );
        }

        throw new LogicException('Submitted data is not an array -> '.json_encode($data));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PickupActivity $object, $return = true)
    {
        //Removes links from pickupActivity to groupActivity
        if (!$object->getGroupActivities()->isEmpty()) {
            foreach ($object->getGroupActivities() as $groupActivity) {
                $this->em->remove($groupActivity);
            }
        }

        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        if ($return) {
            return array(
                'status' => true,
                'message' => 'PickupActivity supprimé',
            );
        }
    }

    /**
     * Deletes PickupActivity by registrationId.
     */
    public function deleteByRegistrationId(int $registrationId)
    {
        $pickupActivities = $this->em->getRepository('App\Entity\PickupActivity')->findByRegistrationId($registrationId);
        if (!empty($pickupActivities)) {
            foreach ($pickupActivities as $pickupActivity) {
                $this->delete($pickupActivity, false);
            }

            return array(
                'status' => true,
                'message' => 'PickupActivity supprimés',
            );
        }
    }

    /**
     * Gets all the PickupActivities by child and date.
     */
    public function findAllByChildDate(int $childId, string $date)
    {
        return $this->em
            ->getRepository('App\Entity\PickupActivity')
            ->findAllByChildDate($childId, $date)
        ;
    }

    /**
     * Gets all the PickupActivities by date.
     */
    public function findAllByDate(string $date)
    {
        return $this->em
            ->getRepository('App\Entity\PickupActivity')
            ->findAllByDate($date)
        ;
    }

    /**
     * Gets all the PickupActivities by status.
     */
    public function findAllByStatus(string $date, string $status)
    {
        return $this->em
            ->getRepository('App\Entity\PickupActivity')
            ->findAllByStatus($date, $status)
        ;
    }

    /**
     * Gets GroupsAge (taken from table parameter).
     */
    public function getGroupAge()
    {
        $groupsAge = $this->em->getRepository('App\Entity\Parameter')->findAllGroupAge();
        foreach ($groupsAge as $group) {
            $groupAge[] = (int) $group->getValue();
        }
        sort($groupAge);

        return $groupAge;
    }

    /**
     * Returns the parameters used for affect.
     *
     * @returns array
     *
     * @throws
     */
    public function getParameters()
    {
        //Defines mandatory parameters that must be set
        $mandatoryParameters = array(
            'maxGroupAgeUnderMax',
            'maxGroupAgeOverMax',
            'groupActivityMorningStart',
            'groupActivityMorningEnd',
            'groupActivityAfternoonStart',
            'groupActivityAfternoonEnd',
            'groupActivityDefaultLocation',
            'groupActivityLunchStart',
            'groupActivityLunchEnd',
        );

        //Defines parameters
        $parameters = array();
        foreach ($mandatoryParameters as $parameter) {
            $parameterValue = $this->em->getRepository('App\Entity\Parameter')->findOneByName($parameter);

            //Assigns value
            if (null !== $parameterValue) {
                $parameters[$parameter] = $parameterValue->getValue();
            //Throws an exception if one of the mandatory parameter is missing
            } else {
                throw new LogicException('Missing mandatory parameter -> "'.$parameter.'", enable it in the table parameter');
            }
        }

        //Defines values to use
        $parameters['maxGroupAgeUnderMax'] = (int) $parameters['maxGroupAgeUnderMax'];
        $parameters['maxGroupAgeOverMax'] = (int) $parameters['maxGroupAgeOverMax'];
        $parameters['groupActivityMorningStart'] = new DateTime('1970-01-01'.$parameters['groupActivityMorningStart']);
        $parameters['groupActivityMorningEnd'] = new DateTime('1970-01-01'.$parameters['groupActivityMorningEnd']);
        $parameters['groupActivityAfternoonStart'] = new DateTime('1970-01-01'.$parameters['groupActivityAfternoonStart']);
        $parameters['groupActivityAfternoonEnd'] = new DateTime('1970-01-01'.$parameters['groupActivityAfternoonEnd']);
        $parameters['groupActivityLunchStart'] = new DateTime('1970-01-01'.$parameters['groupActivityLunchStart']);
        $parameters['groupActivityLunchEnd'] = new DateTime('1970-01-01'.$parameters['groupActivityLunchEnd']);
        $parameters['groupActivityDefaultLocation'] = $this->em->getRepository('App\Entity\Location')->findOneByLocationId((int) $parameters['groupActivityDefaultLocation']);
        $parameters['totalGroupActivity'] = 2;

        return $parameters;
    }


    /**
     * retrieve by date and group child with
     * nb sport, sport, age, sexe
     */
    public function listByChild($date) {

        $pickupActivities = $this->em->getRepository('App\Entity\PickupActivity')->findAllByDateGroup($date);

        $arr = array();
        foreach ($pickupActivities as $pickupActivity) {
            $child = $pickupActivity->getChild();
            $childId = $child->getChildId();

            if(!isset($arr[$child->getAge()][$child->getGender()])) $arr[$child->getAge()][$child->getGender()] = [];


            if(!key_exists($childId, $arr[$child->getAge()][$child->getGender()])) {

                if($pickupActivity->getLastDayOfWeek() != null) {
                    $lastday = $pickupActivity->getLastDayOfWeek()->format('Y-m-d');
                } else {
                    $lastday = null;
                }

                $datas = [
                    'pickupActivityId' => $pickupActivity->getPickupActivityId(),
                    'childId'          => $child->getChildId(),
                    'fullname'         => $child->getFullname(),
                    'fullnameReverse'  => $child->getFullnameReverse(),
                    'gender'           => $child->getGender(),
                    'birthdate'        => $child->getBirthdate()->format('Y-m-d'),
                    'age'              => $child->getAge(),
                    'childHand'        => $child->getChildHand(),
                    'sportifProfil'    => $child->getSportifProfil(),
                    'date'             => $pickupActivity->getDate()->format('Y-m-d'),
                    'start'            => $pickupActivity->getStart()->format('H:i:s'),
                    'end'              => $pickupActivity->getStart()->format('H:i:s'),
                    'sports'           => [
                                            $pickupActivity->getSport()->getSportId() => $pickupActivity->getSport()->getName()
                                        ],
                    'location'         => [ 
                                            $pickupActivity->getLocation()->getLocationId() => $pickupActivity->getLocation()->getName()
                                            ], 
                    'lastDayOfWeek'    => $lastday,
                    'status'           => $pickupActivity->getStatus(),
                ];
                $arr[$child->getAge()][$child->getGender()][$child->getChildId()] = $datas;
            } else {
                $arr[$child->getAge()][$child->getGender()][$child->getChildId()]['sports'][$pickupActivity->getSport()->getSportId()] = $pickupActivity->getSport()->getName();
            }
           
        }

        return $arr;

    }




    /**
     * Returns the PickupActivities for a specific date.
     */
    public function getPickupActivities($date)
    {
        //Gets GroupsAge
        $groupAge = $this->getGroupAge();

        //Defines PickupActivities
        $pickupActivities = $this->findAllByDate($date);
        $pickupsActivityFinal = array();
        foreach ($pickupActivities as $pickupActivity) {
            //If there are no links with GroupActivity AND that PickupActivity is not validated, then adds the PickupActivity with its age group and sport
            if ($pickupActivity->getGroupActivities()->isEmpty() && 'validated' !== strtolower($pickupActivity->getValidated())) {
                $age = $pickupActivity->getChild()->getBirthdate()->diff(new DateTime())->y;
                switch (true) {
                    case $age < $groupAge[0]:
                        $ageGroup = (string) $groupAge[0];
                        break;

                    case $age < $groupAge[1]:
                        $ageGroup = (string) $groupAge[1];
                        break;

                    case $age < $groupAge[2]:
                        $ageGroup = (string) $groupAge[2];
                        break;

                    default:
                        $ageGroup = (string) $groupAge[2].'+';
                        break;
                }

                $sport = 'sport-'.$pickupActivity->getSport()->getSportId();
                $pickupsActivityFinal[$sport][$ageGroup][] = $pickupActivity;
            }
        }

        return $pickupsActivityFinal;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(PickupActivity $object)
    {
        if (null === $object->getDate() ||
            null === $object->getChild()) {
            throw new UnprocessableEntityHttpException('Missing data for PickupActivity -> '.json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(PickupActivity $object, string $data)
    {
        $status_original = $object->getStatus();

        //Submits data
        $data = $this->mainService->submit($object, 'pickup-activity-modify', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        // update presence if status change

        
        if ($status_original != $object->getStatus()) {
            // update presence
            if ($object->getStatus() == 'npec' || $object->getStatus() == 'pec' || $object->getStatus() == null || $object->getStatus() == '') {
                $this->updateChildPresenceStatus($object->getChild(), $object->getDate(), $object->getStatus());
            }

            // update transport
            $allpa = $this->em->getRepository('App\Entity\Pickup')->findAllByChildAndDate($object->getChild(), $object->getDate()->format('Y-m-d'));
            if ($allpa) {
                foreach ($allpa as $mypa) {
                    $mypa->setStatus($object->getStatus());
                    $mypa->setStatusChange(new DateTime());
                    $this->em->persist($mypa);
                    $this->em->flush();
                }
            }

        }

        //Returns data
        return array(
            'status' => true,
            'message' => 'PickupActivity modifié',
            'pickupActivity' => $this->toArray($object),
        );
    }


    public function associatedByRegistration($currentActivity) {

        $arr = [];
        $activitys = $this->em->getRepository('App\Entity\PickupActivity')->findByCreatedAtAndChild($currentActivity->getCreatedAt()->format('Y-m-d'), $currentActivity->getChild());
        foreach($activitys as $activity) {
            $arr[] = $activity->toArray();
        }
        return $arr;
    }

    public function updateChildPresenceStatus($child, $date, $status) {
        $presence = $this->em->getRepository('App\Entity\ChildPresence')->findOneBy(['child' => $child, 'date' => $date]);
        if($status == null) $status = "";
        $presence->setStatus($status);
        $presence->setStatusChange(new DateTime());

        $this->em->persist($presence);
        $this->em->flush();
        return true;
    }

    /**
     * Removes empty GroupActivity non-locked.
     */
    public function removeGroupActivityNonLocked($date)
    {
        $groupActivities = $this->em->getRepository('App\Entity\GroupActivity')->getAllNonLocked($date);
        foreach ($groupActivities as $groupActivity) {
            if ($groupActivity->getStaff()->isEmpty() && $groupActivity->getPickupActivities()->isEmpty()) {
                $this->em->remove($groupActivity);
            }
        }

        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(PickupActivity $object, $mode = "all")
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related registration
        if (null !== $object->getRegistration() && !$object->getRegistration()->getSuppressed()) {


            if($mode == "all") {
                $objectArray['registration'] = $this->mainService->toArray($object->getRegistration()->toArray());
            }


            if(count($object->getRegistration()->getSports())>0) {
                foreach($object->getRegistration()->getSports() as $link) {
                    $sportsArray[] = $link->getSport()->getName(); 
                }
                $objectArray['listSports'] =  implode(',', $sportsArray);

            }
        }

        //Gets related child
        if (null !== $object->getChild() && !$object->getChild()->getSuppressed()) {
            $objectArray['child'] = $this->mainService->toArray($object->getChild()->toArray());
        }

        //Gets related sport
        if (null !== $object->getSport() && !$object->getSport()->getSuppressed()) {
            $objectArray['sport'] = $this->mainService->toArray($object->getSport()->toArray());
        }

        //Gets related location
        if (null !== $object->getLocation() && !$object->getLocation()->getSuppressed()) {
            $objectArray['location'] = $this->mainService->toArray($object->getLocation()->toArray());
        }

        //Gets related groupActivities
        if (null !== $object->getGroupActivities()) {
            $groupActivities = array();
            foreach ($object->getGroupActivities() as $groupActivityLink) {
                if (!$groupActivityLink->getGroupActivity()->getSuppressed()) {
                    $groupActivities[] = $this->mainService->toArray($groupActivityLink->getGroupActivity()->toArray());
                }
            }
            $objectArray['groupActivities'] = $groupActivities;
        }

        return $objectArray;
    }

    /**
     * Unaffects all the PickupActivity from GroupActivity for a specific date.
     */
    public function unaffect($date)
    {
        $counter = 0;
        $pickupsActivity = $this->findAllByDate($date);
        if (!empty($pickupsActivity)) {
            foreach ($pickupsActivity as $pickupActivity) {
                //Unaffects PïckupActivity
                if (!$pickupActivity->getGroupActivities()->isEmpty()) {
                    foreach ($pickupActivity->getGroupActivities() as $groupActivityLink) {
                        if (!$groupActivityLink->getGroupActivity()->getLocked()) {
                            $this->em->remove($groupActivityLink);
                        }
                    }

                    ++$counter;
                    if (20 === $counter) {
                        $this->em->flush();
                        $counter = 0;
                    }
                }
            }
        }
        $this->em->flush();

        //Removes empty GroupActivity non-locked
        $this->removeGroupActivityNonLocked($date);
    }


    public function removeChildFromGroup($groupdId, $childId) {


        if(!$child = $this->em->getRepository('App\Entity\Child')->find($childId)) return ['message' => 'child not found'];

        if(!$group = $this->em->getRepository('App\Entity\GroupActivity')->find($groupdId)) return ['message' => 'group not found'];

        $remove = 0;

        foreach($group->getPickupActivities() as $pickupActivityLink) {

            $pickupActivity = $pickupActivityLink->getPickupActivity();

            if($pickupActivity->getChild()->getChildId() == $childId) {
                $this->em->remove($pickupActivityLink);
                $this->em->flush();
                $remove = 1;
            }

        }

        ($remove == 1) ? $message = "link removed" : $message = "no link found";

        return ['message' => $message];


    }


    /**
     * Return all pickup by day
     *
     * @param $date
     * @return Array
     */
    public function getPickupsADay($date) {

        $rawSql = "
                  SELECT  pa.pickup_activity_id, 
                          ch.child_id, ch.firstname, ch.lastname, CONCAT(UPPER(ch.lastname),' ',ch.firstname) as fullname,
                          YEAR(FROM_DAYS(DATEDIFF(CURRENT_DATE, ch.birthdate))) as age, 
                          pa.registration_id,
                          pa.start, pa.end,
                          pa.status, 
                          pa.sport_id, pa.location_id 
                  FROM pickup_activity pa
                  LEFT JOIN child ch ON ch.child_id = pa.child_id
                  WHERE date = '".$date."'
                  AND pa.suppressed = 0
                  ORDER BY age asc, ch.lastname asc, ch.firstname asc";

        $stmt = $this->em->getConnection()->prepare($rawSql);
        $datas = $stmt->executeQuery([])->fetchAllAssociative();

        return $datas;
    }
}
