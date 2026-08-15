<?php

namespace App\Service;

use App\Entity\StaffPresence;
use App\Entity\TaskStaff;
use App\Entity\Pickup;


use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * StaffPresenceService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class StaffPresenceService implements StaffPresenceServiceInterface
{
    private $em;

    private $staffService;

    private $mainService;

    public function __construct(
        EntityManagerInterface $em,
        StaffServiceInterface $staffService,
        MainServiceInterface $mainService
    )
    {
        $this->em = $em;
        $this->staffService = $staffService;
        $this->mainService = $mainService;
    }

    /**
     * Adds specific data that could not be added via generic method
     */
    public function addSpecificData(StaffPresence $object, array $data)
    {
        //Should be done from StaffPresenceType but it returns null...
        if (array_key_exists('start', $data)) {
            $object->setStart(DateTime::createFromFormat('H:i:s', $data['start']));
        }
        if (array_key_exists('end', $data)) {
            $object->setEnd(DateTime::createFromFormat('H:i:s', $data['end']));
        }
    }


    public function modifyTypeName($staffPresenceId, $typeName) {
      $object = $this->em->getRepository('App\Entity\StaffPresence')->find($staffPresenceId);

      if($typeName == "DELETE") {
          return $this->delete($object);
      } else {
          $object->setTypeName($typeName);
          $this->em->persist($object);
          $this->em->flush();
          return array(
              'status' => true,
              'message' => 'StaffPresence modifiée',
          );
      }
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $data = json_decode($data, true);

        if (is_array($data) && !empty($data)) {
            foreach ($data as $staffPresence) {


                if(!isset($staffPresence['typeName'])) $staffPresence['typeName'] = "PRESENCE";
                $object = $this->em->getRepository('App\Entity\StaffPresence')->findByData($staffPresence);
                //Creates object if not already existing
                if (null === $object) {
                    $object = new StaffPresence();


                    if(!isset($staffPresence['location']) || $staffPresence['location'] == "") $staffPresence['location'] = 6;
                    $location = $this->em->getRepository('App\Entity\Location')->find($staffPresence['location']);

                    $staffPresence['location'] = $location;

                    $this->mainService->create($object);

                    //Submits data
                    $this->mainService->submit($object, 'staff-presence-create', $staffPresence);
                    $this->addSpecificData($object, $staffPresence);

                    //Checks if entity has been filled
                    $this->isEntityFilled($object);

                    $object->setLocation($location);

                    //Persists data
                    $this->mainService->persist($object);
                }
            }

            //Returns data
            return array(
                'status' => true,
                'message' => 'StaffPresence ajoutées',
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . json_encode($data));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(StaffPresence $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'StaffPresence supprimée',
        );
    }

    /**
     * Deletes StaffPresence by array of ids
     */
    public function deleteByArray(string $data)
    {
        $data = json_decode($data, true);
        if (is_array($data) && !empty($data)) {
            foreach ($data as $staffPresence) {
                $object = $this->em->getRepository('App\Entity\StaffPresence')->findByData($staffPresence);

                //Submits data
                if ($object instanceof StaffPresence) {
                    $this->mainService->delete($object);
                    $this->mainService->persist($object);
                }
            }

            return array(
                'status' => true,
                'message' => 'StaffPresence supprimées',
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . json_encode($data));
    }

    /**
     * Returns the workload of each staff between 2 dates
     * @return array
     */
    public function getWorkloads($date_from, $date_to, $staffId = null)
    {
      // get staff
      if($staffId) {
         $staff = $this->em->getRepository('App\Entity\Staff')->find($staffId);
      } else {
        $staff = null;
      }

      $staffPresences = $this->em->getRepository('App\Entity\StaffPresence')->findAllBetweenDates($date_from, $date_to, $staff);

      $workloads = array();
      $currentStaffId = null;
      foreach($staffPresences as $staffPresence) {
            // workloads[staffId][] = [
            //                              'date1' => ['timeStart' => $start, 'timeEnd' => $end, 'firstAction' => $first, 'lastAction' => $last]
            //                              'date2' => ['timeStart' => $start, 'timeEnd' => $end, 'firstAction' => $first, 'lastAction' => $last]
            //                        ]
            $date = $staffPresence->getDate()->format('Y-m-d');
            $staff = $staffPresence->getStaff();

            if($staff) {

                    // retrieve first task
                    $firstTask = $this->em->getRepository('App\Entity\TaskStaff')->findOneByStaffAndDate($staff, $date, 'first');
                    $lastTask  = $this->em->getRepository('App\Entity\TaskStaff')->findOneByStaffAndDate($staff, $date, 'last');

                    // retrieve first PEC
                    $firstPickup = $this->em->getRepository('App\Entity\Pickup')->findOneByStaffAndDate($staff, $date, 'first');
                    $lastPickup = $this->em->getRepository('App\Entity\Pickup')->findOneByStaffAndDate($staff, $date, 'last');


                    /***** FIRST ACTION ****/

                    // test if we need use first task or pickup
                    if($firstTask !== null &&  $firstPickup !== null) {
                        $taskTime = $firstTask->getDateTask()->format('YmdHis');
                        $pickupTime = $firstPickup->getStatusChange()->format('Y-m-d H:i:s');
                        ($taskTime < $pickupTime) ? $caseA = "task" : $caseA = "pickup";
                    } else {
                        if($firstTask) {
                            $caseA = "task";
                        } else if($firstPickup) {
                            $caseA = "pickup";
                        } else {
                            $caseA = null;
                        }
                    }

                    // create FIRSTACTION
                    if($caseA == "task") {
                        $timeStart = $firstTask->getDateTask()->format('Y-m-d H:i:s');
                        $firstAction = $firstTask->getName();
                    }  else if ($caseA == "pickup"){
                        $timeStart = $firstPickup->getStatusChange()->format('Y-m-d H:i:s');
                        $firstAction = "Pickup";
                    } else {
                        $timeStart = null;
                        $firstAction = null;
                    }


                    /***** LAST ACTION ****/

                    // test if we need use last task or pickup
                    if($lastTask && $lastPickup) {
                        $taskTime = $lastTask->getDateTask()->format('YmdHis');
                        $pickupTime = $lastPickup->getStatusChange()->format('Y-m-d H:i:s');
                        ($taskTime > $pickupTime) ? $caseB = "task" : $caseB = "pickup";
                    } else {
                        if($lastTask) {
                            $caseB = "task";
                        } else if($lastPickup) {
                            $caseB = "pickup";
                        } else {
                            $caseB = null;
                        }
                    }

                    // create lastACTION
                    if($caseB == "task") {
                        $timeEnd = $lastTask->getDateTask()->format('Y-m-d H:i:s');
                        $lastAction = $lastTask->getName();
                    }  else if( $caseB == "pickup"){
                        $timeEnd = $lastPickup->getStatusChange()->format('Y-m-d H:i:s');
                        $lastAction = "Dropoff";
                    } else {
                        $timeEnd = null;
                        $lastAction = null;
                    }

                    /***** CREATE WORKLOAD ****/

                    $workload = ['date' => $date,
                                 'startCase' => $caseA,
                                 'endCase' => $caseB, 
                                 'timeStart' => $timeStart, 
                                 'timeEnd' => $timeEnd, 
                                 'firstAction' => $firstAction, 
                                 'lastAction' => $lastAction,
                                 'teamsIdList' => $staffPresence->getTeamsIdList()];
                    $person = $staffPresence->getStaff()->getPerson();

                    if($currentStaffId != $staffPresence->getStaff()->getStaffId()) {
                        $staff = [ 'staffId'  => $staffPresence->getStaff()->getStaffId(), 'fullname' => $person->getFirstname().' '.$person->getLastname()];
                        $workloads[$staffPresence->getStaff()->getStaffId()]['staff'] = $staff;
                    }

                    $workloads[$staffPresence->getStaff()->getStaffId()]['dates'][] = $workload;

                    $currentStaffId = $staffPresence->getStaff()->getStaffId();
            }
      }

      return $workloads;


    }


    public function getPresenceSpeed($date_from, $date_to, $staffId = null)
    {
        // get staff
        if ($staffId) {
            $staff = $this->em->getRepository('App\Entity\Staff')->find($staffId);
        } else {
            $staff = null;
        }


        $rawSql = ' SELECT pe.firstname, pe.lastname, st.staff_id, sp.date, sp.start, sp.end, sp.type_name, sp.location_id, lo.name, sp.teams_id_list FROM staff_presence sp
                    INNER JOIN staff st ON sp.staff_id = st.staff_id
                    INNER JOIN person pe ON st.person_id = pe.person_id
                    INNER JOIN location lo ON sp.location_id = lo.location_id
                    WHERE st.is_active = 1 
                      and sp.suppressed = 0
                    AND sp.date >= "'.$date_from.'" and sp.date <= "'.$date_to.'"';

         if($staffId) {
             $rawSql .= ' AND sp.staff_id =' .$staffId;
             }

        $rawSql .= ' ORDER BY pe.lastname, pe.firstname, sp.date';

        $stmt = $this->em->getConnection()->prepare($rawSql);
        $result = $stmt->executeQuery([])->fetchAllAssociative();

        $arr = [];
        foreach($result as $r) {
            $staff_id = $r['staff_id'];
            if(!isset($arr[$staff_id])) {
                $arr[$staff_id]['staff'] = ['firstname' => $r['firstname'], 'lastname' => $r['lastname'], 'staff_id' => $r['staff_id']];
            }
            unset($r['firstname'], $r['lastname'], $r['staff_id']);
            $arr[$staff_id]['details'][] = $r;
        }
        return $arr;

    }

    /**
     * Returns the list of all staff presence by kind and date
     * @return array
     */
    public function findAllByKindAndDate($kind, $date, $status = null)
    {

        if($status != "PRESENCE") {
            return $this->em
                ->getRepository('App\Entity\StaffPresence')
                ->findAllByKindAndDate($kind, $date, $status)
                ;
        } else {
            $result1 = $this->em
                ->getRepository('App\Entity\StaffPresence')
                ->findAllByKindAndDate($kind, $date, "PRESENCE")
                ;
            $result2 = $this->em
                ->getRepository('App\Entity\StaffPresence')
                ->findAllByKindAndDate($kind, $date, "CATCHING")
                ;
            $allresult = array_merge($result1, $result2);

            return $allresult;
        }


    }

    /**
     * Returns the list of presence by staff
     * @return array
     */
    public function findByStaff($staffId, $date)
    {
        return $this->em
            ->getRepository('App\Entity\StaffPresence')
            ->findByStaff($staffId, $date)
        ;
    }

    /**
     * Returns the list of all staffs presences for the date
     * @return array
     */
    public function findStaffsByPresenceDate($date)
    {
        return $this->em
            ->getRepository('App\Entity\StaffPresence')
            ->findStaffsByPresenceDate($date)
        ;
    }

    /**
     * Returns the all presence by Staff for a specific season
     */
    public function getPresenceBySeasonAndStaff(int $seasonId, $staffId)
    {
        // get staff
         $staff = $this->em->getRepository('App\Entity\Staff')->find($staffId);

        //Gets the season dates
        $season = $this->em->getRepository('App\Entity\Season')->findOneById($seasonId);
        $seasonStart = $season->getDateStart()->format('Y-m-d');
        $seasonEnd = $season->getDateEnd()->format('Y-m-d');

        //Gets the staffPresence within the season
        $staffPresences = $this->em->getRepository('App\Entity\StaffPresence')->findAllBetweenDates($seasonStart, $seasonEnd, $staff);
        $staffPresencesArray = array();
        foreach ($staffPresences as $staffPresence) {
            $staffPresencesArray[$staffPresence->getDate()->format('Y-m-d')] = $staffPresence->toArray();
        }

        $result['staff'] = $staff->toArray();
        $result['presences'] = $staffPresencesArray;

        return $result;
    }


    /**
     * Returns the total of staffPresence for a specific season
     */
    public function getTotals(int $seasonId)
    {
        //Gets the season dates
        $season = $this->em->getRepository('App\Entity\Season')->findOneById($seasonId);
        $seasonStart = $season->getDateStart()->format('Y-m-d');
        $seasonEnd = $season->getDateEnd()->format('Y-m-d');

        //Gets the staffPresence within the season
        $staffPresences = $this->em->getRepository('App\Entity\StaffPresence')->findAllBetweenDates($seasonStart, $seasonEnd);
        $staffPresencesArray = array();
        foreach ($staffPresences as $staffPresence) {
            $staffPresencesArray[$staffPresence->getStaff()->getStaffId()][] = $staffPresence->getDate()->format('Y-m-d');
            $staffPresencesArray[$staffPresence->getStaff()->getStaffId()]['data'] =  array(
                'staffId' => $staffPresence->getStaff()->getStaffId(),
                'firstname' => $staffPresence->getStaff()->getPerson()->getFirstname(),
                'lastname' => $staffPresence->getStaff()->getPerson()->getLastname(),
            );
        }
        unset($staffPresences);

        //Creates the array for the totals
        $i = 0;
        $staffPresencesArrayFinal = array();
        foreach ($staffPresencesArray as $key => $staffPresence) {
            $staffPresencesArrayFinal[$i] = $staffPresence['data'];
            unset($staffPresence['data']);
            $staffPresencesArrayFinal[$i]['total'] = count(array_unique($staffPresence));
            $i++;
        }

        return $staffPresencesArrayFinal;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(StaffPresence $object)
    {
        if (null === $object->getStaff() ||
            null === $object->getDate()) {
            throw new UnprocessableEntityHttpException('Missing data for StaffPresence -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(StaffPresence $object)
    {

        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related staff
        if (null !== $object->getStaff() && !$object->getStaff()->getSuppressed()) {
            $objectArray['staff'] = $this->staffService->toArray($object->getStaff());
        }

        return $objectArray;
    }
}
