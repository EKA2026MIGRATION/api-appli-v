<?php

namespace App\Service;

use App\Entity\GroupActivity;
use App\Entity\GroupActivityStaffLink;
use App\Entity\PickupActivity;
use App\Entity\PickupActivityGroupActivityLink;
use App\Entity\Staff;
use App\Entity\StaffPresence;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpCsFixer\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * GroupActivityService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class GroupActivityService implements GroupActivityServiceInterface
{
    private $em;

    private $mainService;

    private $pickupActivityService;

    private $staffService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        PickupActivityServiceInterface $pickupActivityService,
        StaffServiceInterface $staffService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->pickupActivityService = $pickupActivityService;
        $this->staffService = $staffService;
    }

    /**
     * Adds link between PickupActivity and GroupActivity
     */
    public function addLink(int $pickupActivityId, GroupActivity $object)
    {
        $pickupActivity = $this->em->getRepository('App\Entity\PickupActivity')->findOneById($pickupActivityId);
        if ($pickupActivity instanceof PickupActivity && !$pickupActivity->getSuppressed()) {
            $pickupActivityGroupActivityLink = new PickupActivityGroupActivityLink();
            $pickupActivityGroupActivityLink
                ->setPickupActivity($pickupActivity)
                ->setGroupActivity($object)
            ;
            $this->em->persist($pickupActivityGroupActivityLink);
        }
    }

    /**
     * Adds link betwwen GroupActivity and Staff
     */
    public function addStaff(int $staffId, GroupActivity $object)
    {
        $staff = $this->em->getRepository('App\Entity\Staff')->findOneById($staffId);
        if ($staff instanceof Staff && !$staff->getSuppressed()) {
            $groupActivityStaffLink = new GroupActivityStaffLink();
            $groupActivityStaffLink
                ->setGroupActivity($object)
                ->setStaff($staff)
            ;
            $this->em->persist($groupActivityStaffLink);
            $this->em->flush();
        }
    }
    
    public function findByDateBetween($date, $from, $to) {
        $groups = $this->em->getRepository('App\Entity\GroupActivity')->findByDateBetween($date, $from, $to);
        
        $new_group = array();
        foreach ($groups as $group) {

            //Gets related pickupActivities
            if (null !== $group->getPickupActivities()) {
                $activitys = array();
                foreach($group->getPickupActivities() as $link) {
                    if (!$link->getPickupActivity()->getSuppressed() && $link->getPickupActivity()->getStatus() != "npec") {
                        $activity = $link->getPickupActivity();
                        $child = $activity->getChild();
                        $activitys[$child->getChildId()] = [ 
                                                                    'firstname' => $child->getFirstname(),
                                                                    'lastname'  => $child->getLastname(),
                                                                    'photo'     => $child->getPhoto()
                        ];
                    }
                }
            }

            //Gets related staff
            if (null !== $group->getStaff()) {
                $staffs = array();
                foreach($group->getStaff() as $link) {
                    if (!$link->getStaff()->getSuppressed()) {
                        $staffs[$link->getStaff()->getStaffId()] = [
                                                                        'name' => $link->getStaff()->getPerson()->getFirstname(),
                                                                        'photo' => $link->getStaff()->getPerson()->getPhoto()
                        ];
                    }
                }
            }
            

            $new_group[] = [
                            'start' => $group->getStart()->format('H:i'),
                            'end'   => $group->getEnd()->format('H:i'),
                            'age'   => $group->getAge(),
                            'lunch' => $group->getLunch(),
                            'location' => $group->getLocation()->getName(),
                            'area'  => $group->getArea(),
                            'staffs' => $staffs,
                            'sport' => $group->getSport()->getName(),
                            'childs' => $activitys
                           
            ];


        };

        return $new_group;
    }


    public function listByLunchGroup($date) {

          $groups = $this->em->getRepository('App\Entity\GroupActivity')->findLunchgroup($date);
          $arr = [];
          foreach($groups as $group) {
            $arr[] = $group->getArrayOptimise();
          }
          return $arr;
    }

    /**
     * Adds specific data that could not be added via generic method
     */
    public function addSpecificData(GroupActivity $object, array $data)
    {
        //Should be done from GroupActivityType but it returns null...
        if (array_key_exists('start', $data)) {
            $object->setStart(DateTime::createFromFormat('H:i:s', $data['start']));
        }
        if (array_key_exists('end', $data)) {
            $object->setEnd(DateTime::createFromFormat('H:i:s', $data['end']));
        }

        //Converts to boolean
        if (array_key_exists('lunch', $data)) {
            $object->setLunch((bool) $data['lunch']);
        }
        if (array_key_exists('locked', $data)) {
            $object->setLocked((bool) $data['locked']);
        }

        //Adds links from pickupActivity to groupActivity
        if (array_key_exists('links', $data)) {
            //Deletes old links
            $oldLinks = $object->getPickupActivities();
            if (null !== $oldLinks && !empty($oldLinks)) {
                foreach ($oldLinks as $oldLink) {
                    $this->em->remove($oldLink);
                    $this->em->flush();

                }
            }

            //Adds new links
            $links = $data['links'];
            if (null !== $links && is_array($links) && !empty($links)) {
                foreach ($links as $link) {
                    $this->addLink((int) $link['pickupActivityId'], $object);
                    $this->em->flush();

                }
            }
        }

        //Adds links from groupActivity to staff
        // Deletes old links
        $oldLinks = $object->getStaff();
        if (null !== $oldLinks && !empty($oldLinks)) {
            foreach ($oldLinks as $oldLink) {
                $this->em->remove($oldLink);
            }
            $this->em->flush();
        }

        if (array_key_exists('staff', $data)) {

            // Adds new links
            $staff = $data['staff'];
            if (null !== $staff && is_array($staff) && !empty($staff)) {
                foreach ($staff as $staffData) {
                   if($staffData['staffId'] > 0) {
                        $this->addStaff((int) $staffData['staffId'], $object);
                   }
                }
                $this->em->flush();

            }
        }

    }

    public function duplicateGroup($source, $options = []) {


        (isset($options['target_date'])) ? $target_date = $options['target_date'] : $target_date = $source->getDate();
        ($source->getArea() == null) ? $area = "" : $area = $source->getArea();
        (isset($options['start'])) ? $target_start = $options['start'] : $target_start = $source->getStart();
        (isset($options['end'])) ? $target_end = $options['end'] : $target_end = $source->getEnd();
        (isset($options['isLunch'])) ? $isLunch = $options['isLunch'] : $isLunch = $source->getLunch();

        if($isLunch) {
            $sport = $this->em->getRepository('App\Entity\Sport')->find(10); 
        } else {
            $sport = $source->getSport();
        }

        $group_t = new GroupActivity();
        $group_t->setDate($target_date);
        $group_t->setName($source->getName());
        $group_t->setAge($source->getAge());
        $group_t->setStart($target_start);
        $group_t->setEnd($target_end);
        $group_t->setLunch($isLunch);
        $group_t->setComment($source->getComment());
        $group_t->setLocation($source->getLocation());
        $group_t->setArea($area);
        $group_t->setSport($sport);
        
        $userId = 99;
        $group_t->setCreatedAt(new DateTime());
        $group_t->setCreatedBy($userId);
        $group_t->setUpdatedAt(new DateTime());
        $group_t->setUpdatedBy($userId);
        $group_t->setSuppressed(0);

        $this->em->persist($group_t);
        $this->em->flush();


        // copy staff
        foreach($source->getStaff() as $link) {
            $staff = $link->getStaff();

            $linkStaffGroup = new GroupActivityStaffLink();
            $linkStaffGroup->setGroupActivity($group_t);
            $linkStaffGroup->setStaff($staff);

            // persist link
            $this->em->persist($linkStaffGroup);
            $this->em->flush();

            // persist group_t
            $group_t->addStaff($linkStaffGroup, false);
            $this->em->persist($group_t);
            $this->em->flush();
            
        }

            // copy staff
        foreach($source->getPickupActivities() as $link) {
            $activity = $link->getPickupActivity();


            $activityStart = $activity->getStart()->format('Hi');
            $activityEnd   = $activity->getEnd()->format('Hi');

            $groupStart    = $group_t->getStart()->format('Hi');
            $groupEnd      = $group_t->getEnd()->format('Hi');

            // add activity to group_t
            if($activityStart <= $groupStart  && $activityEnd >= $groupEnd) {
                $conditions = [
                                'child' => $activity->getChild(),
                                'sport' => $sport,
                                'date'  => $group_t->getDate()
                ];

                if($lunchActivity = $this->em->getRepository('App\Entity\PickupActivity')->findOneBy($conditions)) {

                    $activity = $lunchActivity;

                    $link = new PickupActivityGroupActivityLink();
                    $link->setPickupActivity($activity);
                    $link->setGroupActivity($group_t);

                    // persist ling
                    $this->em->persist($link);
                    $this->em->flush();

                    // persist group_t
                    $group_t->addPickupActivity($link);
                    $this->em->persist($group_t);
                    $this->em->flush();
                }
                
                  
            }
        }

        return $group_t;
    }


    public function duplicateMoment($data) {
        $data  = json_decode($data, true);
        $startDate = new DateTime();
        $el = explode(':', $data['targetMoment']);
        $startDate->setTime(intval($el[0]), intval($el[1]));
        $minEnd = intval($el[1]) + 45;
        $hourEnd = intval($el[0]);
        if($minEnd > 59) {
            $minEnd = $minEnd - 60;
            $hourEnd++;
        }
        $endDate = new DateTime();
        $endDate->setTime($hourEnd, $minEnd);


        if($data['lunch'] == 1) {
            $isLunch = true;
        } else {
            $isLunch = false;
        }


        foreach(json_decode($data['groupsId']) as $group_id) {
            $group = $this->em->getRepository('App\Entity\GroupActivity')->find($group_id);
            $target = $this->duplicateGroup($group, ['start' => $startDate, 'end' => $endDate, 'isLunch' => $isLunch]);
        } 

        return [];

    }

    public function duplicateRecursive($source, $target) {

        $debug = []; $message = '';

        $messages = [];

        $flush = true;

        $margeTime = 70; // 70 <=> 30 minutes

        // A retrieve all group from source
        if(!$source_groups = $this->findAllByDate($source)) return ['message' => 'no_groups_founded_in_source'];

        // A Bis retrieve ALL PA in target day
        if(!$target_activitys = $this->em->getRepository('App\Entity\PickupActivity')->findAllByDate($target)) return ['message' => 'no_activity_on_target_day'];

        // create an array with child and all activity create by child (array of child present with an activity)
        foreach($target_activitys as $activity_t) {

            if( !$activity_t->getSport() ) continue;

            $childPresenceTargets[$activity_t->getChild()->getChildId()][$activity_t->getSport()->getSportId()] = [
                                                                                                                    'start'   => $activity_t->getStart()->format('Hi'),
                                                                                                                    'end'     => $activity_t->getEnd()->format('Hi'),
                                                                                                                    'activity' => $activity_t
                                                                                                             ];
            if(!$flush) {
                $childPresenceTargetsDebug[$activity_t->getChild()->getChildId()][$activity_t->getSport()->getSportId()] = [
                    'start'   => $activity_t->getStart()->format('Hi'),
                    'end'     => $activity_t->getEnd()->format('Hi'),
                    'activity' => $activity_t->toArray()
             ];
             $debug['childPresenceTargets'] = $childPresenceTargetsDebug;  
            }                                                                                                    
        }


        // B List all coach present in target day
        $presenceStaffs =  $this->em->getRepository('App\Entity\StaffPresence')->findStaffsByPresenceDate($target);
        foreach($presenceStaffs as $presenceStaff) {
            $staffTarget[$presenceStaff->getStaff()->getStaffId()] = $presenceStaff->getStaff();
            if(!$flush) $debug['staffTarget'][$presenceStaff->getStaff()->getStaffId()] = $presenceStaff->getStaff()->getPerson()->getFirstname().' '.$presenceStaff->getStaff()->getPerson()->getLastname();
        }


        // C create group in target GROUP_TARGET
        foreach($source_groups as $group_s) {

            // create target date object
             $target_date = new DateTime($target);
        
    
             // create target group
             ($group_s->getArea() == null) ? $area = "" : $area = $group_s->getArea();
 
             $group_t = new GroupActivity();
             $group_t->setDate($target_date);
             $group_t->setName($group_s->getName());
             $group_t->setAge($group_s->getAge());
             $group_t->setStart($group_s->getStart());
             $group_t->setEnd($group_s->getEnd());
             $group_t->setLunch($group_s->getLunch());
             $group_t->setComment($group_s->getComment());
             $group_t->setLocation($group_s->getLocation());
             $group_t->setArea($area);
             $group_t->setSport($group_s->getSport());
             
             $userId = 99;
             $group_t->setCreatedAt(new DateTime());
             $group_t->setCreatedBy($userId);
             $group_t->setUpdatedAt(new DateTime());
             $group_t->setUpdatedBy($userId);
             $group_t->setSuppressed(0);
 
             $this->em->persist($group_t);
             if($flush) $this->em->flush();

             $target_groups[$group_s->getgroupActivityId()] = $group_t;
             if(!$flush) $debug['target_group'][$group_s->getGroupActivityId()] = $group_t->toArray();
 
        }

   
        // D Loop on groupe source and retrieve groupe target to update
        foreach($source_groups as $group_s) {

            if(!$group_s->getSport()) continue;

             // group data source
             $sport_id = ($group_s->getSport()) ? $group_s->getSport()->getSportId() : 0;
             $start    = intval($group_s->getStart()->format('Hi'));
             $end      = intval($group_s->getEnd()->format('Hi'));

             // groupe t associated
             $group_t  = $target_groups[$group_s->getgroupActivityId()];

            // add staff
            if($group_s->getStaff()) {

                $sourceStaffArray = [];
                foreach($group_s->getStaff() as $staffLink) {
                    $staff_s = $staffLink->getStaff();

                    $sourceStaffArray[] = ['staffId' => $staff_s->getStaffId(), 'staffName' => $staff_s->getPerson()->getFirstname().' '.$staff_s->getPerson()->getLastname()];


                    // check if staff is present on target day
                    if(key_exists($staff_s->getStaffId(), $staffTarget) && ($staff_s instanceof Staff) )  {

                        if ($staff_s instanceof Staff) {
                            $linkStaffGroup = new GroupActivityStaffLink();
                            $linkStaffGroup->setGroupActivity($group_t);
                            $linkStaffGroup->setStaff($staff_s);

                            // persist link
                            $this->em->persist($linkStaffGroup);
                            if($flush) $this->em->flush();
            
                            // persist group_t
                            $group_t->addStaff($linkStaffGroup, false);
                            $this->em->persist($group_t);
                            if($flush) $this->em->flush();
                        }

                    }  else {
                        $messages['staff_not_founded_on_target_day'][] = $staff_s->getPerson()->getFullname();
                    }
        
                }

                $group_s->sourceStaffArray = $sourceStaffArray;
            }


            // get all acivitiy in group_s
            foreach($group_s->getPickupActivities() as $activity_link_s) {


                // activity source
                $activity_s = $activity_link_s->getPickupActivity();

                // check if child_soruce has an activity_TARGET
                $child_id = $activity_s->getChild()->getChildId();
                if(key_exists($child_id, $childPresenceTargets)) {

                    $childDataTarget = $childPresenceTargets[$child_id];
                    
                    // if child source has an activity in target date
                    if(key_exists($sport_id, $childDataTarget)) {

                        $chilDataActivityTarget = $childDataTarget[$sport_id];


                        // check if group time is beetween child presence activity
                        $childActivityTargetStart = intval($chilDataActivityTarget['start']) - $margeTime;
                        $childActivityTargetEnd   = intval($chilDataActivityTarget['end']) + $margeTime;


                        if($childActivityTargetStart <= $start &&  $end <= $childActivityTargetEnd) {

                            $activity_t = $chilDataActivityTarget['activity'];
                            
                            // add activity to group_t
                            $link = new PickupActivityGroupActivityLink();
                            $link->setPickupActivity($activity_t);
                            $link->setGroupActivity($group_t);

                            // persist ling
                            $this->em->persist($link);
                            if($flush) $this->em->flush();

                            // persist group_t
                            $group_t->addPickupActivity($link);
                            $this->em->persist($group_t);
                            if($flush) $this->em->flush();

                            ($activity_s->getSport()) ? $sportname = $activity_s->getSport()->getName() : $sportname = "pas de sport";

                            $messages['child_founded_and_updated'][] = $activity_t->getChild()->getFullnameReverse().' '.$sportname; 

                        } else {
                            $messages['child_founded_but_presence_is_not_compatible'][] = $activity_s->getChild()->getFullnameReverse().
                                                                                               ' Présence :'.$childActivityTargetStart.'-'.$childActivityTargetEnd.
                                                                                               ' - Groupe : '.$start.'-'.$end;
                        }


                    } else {

                        if(!$group_s->getSport()) continue;

                        // if it activity is lunch , add to lunch
                        if($group_s->getSport()->getSportId() == 10) {

                            ($activity_s->getSport()) ? $sportname = $activity_s->getSport()->getName() : $sportname = "pas de sport";


                            $messages['child_founded_but_cant_add_to_lunch'][] = $activity_s->getChild()->getFullnameReverse().' '.$sportname;


                        } else {

                            $messages['child_founded_but_has_not_sport_source_on_target'][] = $activity_s->getChild()->getFullnameReverse().' '.$activity_s->getSport()->getName();
                            
    
                            $messages['forced_id_child_list'][] = [ 
                                                                    'pickup_activity_id'     => $activity_s->getPickupActivityId(),
                                                                    'child_id'               => $activity_s->getChild()->getChildId(),
                                                                    'child_name'             => $activity_s->getChild()->getFullnameReverse(),
                                                                    'sport_id_start'         => $activity_s->getSport()->getSportId(),
                                                                    'sport_name_start'       => $activity_s->getSport()->getName(),
                                                                    'group_start_id'         => $group_s->getgroupActivityId(),
                                                                    'group_start_time'       => $group_s->getStart()->format('H:i:s').'|'.$group_s->getEnd()->format('H:i:s'),
                                                                    'group_start_sport_id'   => $group_s->getSport()->getSportId(),
                                                                    'group_start_sport_name' => $group_s->getSport()->getName(),
                                                                    'group_start_staff'      => $group_s->sourceStaffArray,
                                                                    'group_start_age'        => $group_s->getAge()
    
                                                                    ];

                        }
                    }

                } else {
                    $messages['child_not_founded_on_target'][] = $activity_s->getChild()->getFullnameReverse();
                }
            }
        }


        foreach($messages as $key => $datas) {
            sort($datas);
            $arr[$key] = $datas; 
        }

        if(!$flush) {
            $returnData = ['messages' => $arr, 'debug' => $debug];
        }  else {
            $returnData = ['messages' => $arr];
        }
       
        return $returnData;
    }


    public function searchByCriterias($data) {
        $data = json_decode($data, true);
        if (is_array($data) && !empty($data)) {
            

            $sport = $this->em->getRepository('App\Entity\Sport')->find($data['sport_id']);

            $groups = $this->em->getRepository('App\Entity\GroupActivity')->findAllBySportAndDateTime($data['date'], $sport, $data['start'], $data['end']);

            if(!$groups) return ['no_groups_founded'];

            foreach(explode(',',$data['staff_string']) as $staffId) {
                $searchStaffIds[] = $staffId;
            }


            foreach($groups as $group) {

                if (null !== $group->getStaff()) {
                    $staffs = array();
                    foreach($group->getStaff() as $link) {
                        if (!$link->getStaff()->getSuppressed()) {
                            $staffs[] = $link->getStaff()->getFullname();
                        }
                    }

                    $staffString = implode(',', $staffs);
                } else {
                    $staffString = null;
                }
                
                $time = $group->getStart()->format('H:i').'|'.$group->getEnd()->format('H:i');

                ($group->getAge() > 1) ? $age = $group->getAge() : $age = '/'; 
    
                $currentGroup = [
                                    'groupdId'  => $group->getGroupActivityId(),
                                    'groupInfo' => "Staffs: ".$staffString." - Sport: ".$group->getSport()->getName().' - '.$age.' ans - '.$time  
                                ];

                $typename = ['others', 'match', 'ideal'];


                $type = 0;

                //match age
                if($group->getAge() === $data['age']) {
                    $type++;
                } 

                // match staff
                $staffIn = false;
                foreach($group->getStaff() as $staffLink) {

                    $currentGroupStaffId = $staffLink->getStaff()->getStaffId();
                    //$arr["staff"][] = $staffLink->getStaff()->getStaffId().' '.$group->getAge();

                    if(in_array($currentGroupStaffId, $searchStaffIds)) $staffIn = true;
                }

                if($staffIn == true) $type++;
                
                $arr[$typename[$type]][] = $currentGroup;


            }

            ksort($arr);

            return $arr;





        } else {
            return ['not data array sended'];
        }
    }

    /**
     *
     * return all groups by date
     *
     * @param $date
     * @return mixed[]
     */
    public function getGroupADay($date) {


        $rawSql = "
                  SELECT  ga.group_activity_id, ga.name, ga.age, ga.start, ga.end,
                          ga.lunch, ga.location_id, ga.sport_id, ga.area, 
                          pa.pickup_activity_id, pa.child_id
                  FROM group_activity ga 
                  LEFT JOIN pickup_activity_group_activity_link pga ON pga.group_activity_id = ga.group_activity_id
                  LEFT JOIN pickup_activity pa ON pa.pickup_activity_id = pga.pickup_activity_id
                  LEFT JOIN child c ON c.child_id = pa.child_id
                  WHERE ga.date = '".$date."'
                  AND ga.suppressed = 0 
                  ORDER by ga.start;";

        $stmt = $this->em->getConnection()->prepare($rawSql);
        $groups_data = $stmt->executeQuery([])->fetchAllAssociative();


        $rawSql = "
                SELECT groupjoin.group_activity_id as group_id, groupjoin.staff_id as staff_id
                FROM group_activity_staff_link groupjoin 
                LEFT JOIN group_activity ga ON ga.group_activity_id = groupjoin.group_activity_id 
                WHERE ga.date = '".$date."'
                ORDER BY ga.group_activity_id;";

        $stmt = $this->em->getConnection()->prepare($rawSql);
        $datas = $stmt->executeQuery([])->fetchAllAssociative();
        $staffs_data = [];
        foreach($datas as $data) {
            $staffs_data[$data['group_id']][] = $data['staff_id'];
        }

        return ['groups' => $groups_data, 'staffs' => $staffs_data];

    }


    public function getGroupToday()
    {

        $today = date("Y-m-d");

        $rawSql = " SELECT
                    ga.group_activity_id, ga.name, ga.age, ga.start, ga.end,
                    ga.lunch, ga.location_id, ga.sport_id, ga.area, 
                    pa.pickup_activity_id, pa.child_id, c.firstname, c.lastname,
                    p.firstname as coach_firstname, p.lastname as coach_lastname, st.staff_id
                    FROM group_activity ga 
                    LEFT JOIN pickup_activity_group_activity_link pga ON pga.group_activity_id = ga.group_activity_id
                    LEFT JOIN pickup_activity pa ON pa.pickup_activity_id = pga.pickup_activity_id
                    LEFT JOIN child c ON c.child_id = pa.child_id
                    LEFT JOIN group_activity_staff_link link ON link.group_activity_id = ga.group_activity_id
                    LEFT JOIN staff st ON st.staff_id = link.staff_id
                    LEFT JOIN person p ON p.person_id = st.person_id
                    WHERE ga.date = '".$today."'
                    AND ga.suppressed = 0 
                    ORDER by ga.group_activity_id, ga.start;
                ";

        $stmt = $this->em->getConnection()->prepare($rawSql);
        $groups_data = $stmt->executeQuery([])->fetchAllAssociative();

        $arr = [];
        foreach($groups_data as $data) {

            $group_id = $data['group_activity_id'];
            $child_id = $data['child_id'];
            $staff_id = $data['staff_id'];
            $staff_name = $data['coach_firstname'].' '.$data['coach_lastname'];
            $child_name = $data['firstname'].' '.$data['lastname'];

            $arr[$group_id]['coachs'][$staff_id] = $staff_name;
            $arr[$group_id]['childs'][$child_id] = $child_name;
            $arr[$group_id][$group_id] = [
                                            'start' => $data['start'],
                                            'end'   => $data['end'],
                                            'age'   => $data['age'],
                                            'sport_id' => $data['sport_id'],
                                            'location_id' => $data['location_id'],
                                        ];
        }

        return $arr;
    }




    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new GroupActivity();
        $this->mainService->create($object);
        $this->mainService->persist($object);

        $data = $this->mainService->submit($object, 'group-activity-create', $data);
        $this->mainService->persist($object);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'GroupActivity ajouté',
            'groupActivity' => $this->toArray($object),
        );
    }


    public function deleteAllByDate($date) {
        $groups = $this->em->getRepository('App\Entity\GroupActivity')->findAllByDate($date);
        foreach($groups as $group) {
            $this->delete($group);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function createMultiple(string $data)
    {
        $data = json_decode($data, true);
        if (is_array($data) && !empty($data)) {
            foreach ($data as $groupActivityData) {
                //Submits data
                $object = new GroupActivity();
                $this->mainService->create($object);
                $this->mainService->submit($object, 'group-activity-create', $groupActivityData);
                $this->addSpecificData($object, $groupActivityData);

                //Checks if entity has been filled
                $this->isEntityFilled($object);

                //Persists data
                $this->mainService->persist($object);
            }

            //Returns data
            return array(
                'status' => true,
                'message' => 'GroupActivities ajoutés',
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . json_encode($data));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(GroupActivity $object)
    {
        //Removes links from pickupActivity to groupActivity
        $objectPickupActivityLinks = $this->em->getRepository('App\Entity\PickupActivityGroupActivityLink')->findByGroupActivity($object);
        foreach ($objectPickupActivityLinks as $objectPickupActivityLink) {
            if ($objectPickupActivityLink instanceof PickupActivityGroupActivityLink) {
                $this->em->remove($objectPickupActivityLink);
            }
        }

        //Removes links from groupActivity to staff
        $objectStaffLinks = $this->em->getRepository('App\Entity\GroupActivityStaffLink')->findByGroupActivity($object);
        foreach ($objectStaffLinks as $objectStaffLink) {
            if ($objectStaffLink instanceof GroupActivityStaffLink) {
                $this->em->remove($objectStaffLink);
            }
        }

        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'GroupActivity supprimé',
        );
    }

    /**
     * Returns the list of all groupActivities by date
     * @return array
     */
    public function findAllByDate(string $date)
    {
        return $this->em
            ->getRepository('App\Entity\GroupActivity')
            ->findAllByDate($date)
        ;
    }

    /**
     * Returns the GroupActivities linked to date and staff
     * @return array
     */
    public function findAllByDateByStaff(string $date, $staff)
    {
        return $this->em
            ->getRepository('App\Entity\GroupActivity')
            ->findAllByDateByStaff($date, $staff)
        ;
    }

    /**
     * Returns the groupActivity correspoonding to groupActivityId
     * @return array
     */
    public function findOneById(int $groupActivityId)
    {
        return $this->em
            ->getRepository('App\Entity\GroupActivity')
            ->findOneById($groupActivityId)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(GroupActivity $object)
    {
        if (null === $object->getDate() ||
            null === $object->getStart()) {
            throw new UnprocessableEntityHttpException('Missing data for GroupActivity -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(GroupActivity $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'group-activity-modify', $data);
        $this->addSpecificData($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'GroupActivity modifié',
            'groupActivity' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(GroupActivity $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related location
        if (null !== $object->getLocation() && !$object->getLocation()->getSuppressed()) {
            $objectArray['location'] = $this->mainService->toArray($object->getLocation()->toArray());
        }

        //Gets related sport
        if (null !== $object->getSport() && !$object->getSport()->getSuppressed()) {
            $objectArray['sport'] = $this->mainService->toArray($object->getSport()->toArray());
        }

        //Gets related pickupActivities
        if (null !== $object->getPickupActivities()) {
            $pickupActivities = array();
            foreach($object->getPickupActivities() as $pickupActivityLink) {
                if (!$pickupActivityLink->getPickupActivity()->getSuppressed()) {
                    $pickupActivities[] = $this->pickupActivityService->toArray($pickupActivityLink->getPickupActivity());
                }
            }
            $objectArray['pickupActivities'] = $pickupActivities;
        }

        //Gets related staff
        if (null !== $object->getStaff()) {
            $staff = array();
            foreach($object->getStaff() as $groupActivityStaffLink) {
                if (!$groupActivityStaffLink->getStaff()->getSuppressed()) {
                    $staff[] = $this->staffService->toArray($groupActivityStaffLink->getStaff());
                }
            }
            $objectArray['staff'] = $staff;
        }

        return $objectArray;
    }
}
