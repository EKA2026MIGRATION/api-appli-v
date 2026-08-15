<?php

namespace App\Service;

use App\Entity\TaskStaff;
use App\Entity\Staff;
use DateTime;

use App\Service\StaffServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
* TaskStaffService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class TaskStaffService implements TaskStaffServiceInterface
{
    private $em;

    private $mainService;

    public function __construct(
        EntityManagerInterface $em,
        StaffServiceInterface $staffService,
        MainServiceInterface $mainService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->staffService = $staffService;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyStep(string $data)
    {
        $values = json_decode($data, true);

        $task_staff_id = $values['task_staff_id'];
        $step          = $values['step'];

        $taskStaff = $this->em->getRepository('App\Entity\TaskStaff')->find($task_staff_id);
        $taskStaff->setStep(mb_strtoupper($step));

        if($step == "DONE") {
          $taskStaff->setDateTaskDone(new \DateTime());
        } else {
          $taskStaff->setDateTaskDone(null);
        }

        $this->mainService->modify($taskStaff);

        //Persists data
        $this->mainService->persist($taskStaff);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Tache modifiée' ,
            'task_staff_id' => $taskStaff->getId()
        );



    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $values = json_decode($data, true);

        isset($values['task_id'])        ?  $task_id = $values['task_id'] : $task_id = null;
        isset($values['remote_address']) ?  $remoteAddress = $values['remote_address'] : $remoteAddress = null;
        isset($values['staff_id'])       ?  $staff_id = $values['staff_id'] : $staff_id = null;
        isset($values['supervisor_id'])  ?  $supervisor_id = $values['supervisor_id'] : $supervisor_id = null;
        isset($values['duration'])       ?  $duration = $values['duration'] : $duration = null;
        isset($values['date_limit'])     ?  $dateL = $values['date_limit'] : $dateL = $values['date_task'];;
        isset($values['type'])           ?  $type = $values['type'] : $type = null;
        isset($values['criticity'])      ?  $criticity = $values['criticity'] : $criticity = 99;


        if(!$task_id) $type = "basic";

        if (is_array($values) && !empty($values)) {


            ($staff_id) ? $staff = $this->em->getRepository('App\Entity\Staff')->find($staff_id) : $staff = null;
            ($task_id) ? $task  = $this->em->getRepository('App\Entity\Task')->find($task_id) : $task = null;
            ($supervisor_id) ? $supervisor  = $this->em->getRepository('App\Entity\Staff')->find($supervisor_id) : $supervisor = null;
            ($values['name'] == null && $task != null) ? $name = $task->getName() : $name = $values['name'];

            $dateLimit = new DateTime($dateL);
            $dateTask = new DateTime($values['date_task']);

            $object = new TaskStaff();
            $object->setName($name);
            $object->setDescription($values['description']);
            $object->setSupervisor($supervisor);
            $object->setStep($values['step']);
            $object->setTask($task);
            $object->setStaff($staff);
            $object->setDateTask($dateTask);
            $object->setDateLimit($dateLimit);
            $object->setDuration($duration);
            $object->setType($type);
            $object->setCriticity($criticity);
            $object->setRemoteAddress($remoteAddress);


            if($values['step'] == "DONE") {
              $object->setDateTaskDone(new \DateTime());
            } else {
              $object->setDateTaskDone(null);
            }

            $this->mainService->create($object);

            //Persists data
            $this->mainService->persist($object);

            if($supervisor != null) {

                $object2 = new TaskStaff();
                $object2->setName('SUPERVISION: '.$staff->getPerson()->getFirstname().' '.$staff->getPerson()->getLastname().' - '.$name);
                $object2->setDescription($values['description']);
                $object2->setSupervisor(null);
                $object2->setStep($values['step']);
                $object2->setTask($task);
                $object2->setStaff($supervisor);
                $object2->setDateTask($dateTask);
                $object2->setDateLimit($dateLimit);
                $object2->setDuration(null);
                $object2->setCriticity($criticity);
                $object2->setRemoteAddress($remoteAddress);
                $object2->setType($type);

                if($values['step'] == "DONE") {
                  $object2->setDateTaskDone(new \DateTime());
                } else {
                  $object2->setDateTaskDone(null);

                }

                $this->mainService->create($object2);

                //Persists data
                $this->mainService->persist($object2);
            }

            //Returns data
            return array(
                'status' => true,
                'message' => 'Tache ajoutée' ,
                'task_staff_id' => $object->getId(),
                'task' => $object->toArray()
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . json_encode($data));
    }



    /**
     * {@inheritdoc}
     */
    public function update(string $data)
    {
        $values = json_decode($data, true);

        $object = $this->em->getRepository('App\Entity\TaskStaff')->find($values['id']);
        isset($values['supervisor_id']) ?  $supervisor_id = $values['supervisor_id'] : $supervisor_id = null;
        ($supervisor_id) ? $supervisor  = $this->em->getRepository('App\Entity\Staff')->find($supervisor_id) : $supervisor = null;
        isset($values['staff_id']) ?  $staff_id = $values['staff_id'] : $staff_id = null;
        ($staff_id) ? $staff = $this->em->getRepository('App\Entity\Staff')->find($staff_id) : $staff = null;


        if($object->getSupervisor() != $supervisor) {
            // change supervisor
            $task_name_super = 'SUPERVISION: '.$object->getStaff()->getPerson()->getFirstname().' '.$object->getStaff()->getPerson()->getLastname().' - '.$object->getName();
            $task_supervisor = $this->em->getRepository('App\Entity\TaskStaff')->findOneBy(['name' => $task_name_super]);

            if($task_supervisor) {
              $this->em->remove($task_supervisor);
              $this->em->flush();
            }

            $new_supervisor = 1;

        }


        $object->setName($values['name']);
        $object->setDescription($values['description']);
        $object->setSupervisor($supervisor);
        $object->setStep($values['step']);
        $object->setTask(null);
        $object->setStaff($staff);
        $object->setDateLimit(new DateTime($values['date_limit']));
        $object->setDuration($values['duration']);
        $object->setDateTask( new DateTime($values['date_task']));
        $object->setRemoteAddress($values['remote_address']);
        $object->setType($values['type']);

        if($values['step'] == "TODO") {
          $object->setDateTaskDone(null);
        } else {
          if(isset($values['date_task_done'])) {
            $object->setDateTaskDone(new \DateTime($values['date_task_done']));
          }
        }

        if(isset($new_supervisor)) {
            // create task for supervisor
            $object2 = new TaskStaff();
            $object2->setName('SUPERVISION: '.$staff->getPerson()->getFirstname().' '.$staff->getPerson()->getLastname().' - '.$object->getName());
            $object2->setDescription($values['description']);
            $object2->setSupervisor(null);
            $object2->setStep($values['step']);
            $object2->setTask(null);
            $object2->setStaff($supervisor);
            $object2->setDateLimit(new DateTime($values['date_limit']));
            $object2->setDuration(null);
            $object2->setDateTask(new DateTime($values['date_task']));
            $object2->setRemoteAddress($values['remote_address']);
            $object2->setType($values['type']);


            if($values['step'] == "TODO") {
              $object->setDateTaskDone(null);
            } else {
              if(isset($values['date_task_done'])) {
                $object->setDateTaskDone(new \DateTime($values['date_task_done']));
              }
            }
            $this->mainService->create($object2);

            //Persists data
            $this->mainService->persist($object2);

        }


        $this->mainService->modify($object);

        //Persists data
        $this->mainService->persist($object);


        //Returns data
        return array(
            'status' => true,
            'message' => 'Tache modifiée' ,
            'task_staff_id' => $object->getId()
        );


    }





    public function delete(string $data)
    {
        $values = json_decode($data, true);

        $taskStaffId = $values['taskStaffId'];
        $taskStaff   = $this->em->getRepository('App\Entity\TaskStaff')->find($taskStaffId);

        $this->em->remove($taskStaff);
        $this->em->flush();

        return array(
            'status' => true,
            'message' => 'Tache supprimée' ,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveTasks($staffId, $date)
    {

        $staff = $this->em->getRepository('App\Entity\Staff')->find($staffId);
        $tasks = $this->em->getRepository('App\Entity\TaskStaff')->findByStaffAndDate($staff, $date);
        return $tasks;
    }

    /**
     * {@inheritdoc}
     */
    public function list($date)
    {
        $tasks = $this->em->getRepository('App\Entity\TaskStaff')->findByDate($date);
        return $tasks;
    }

    /**
     * {@inheritdoc}
     */
    public function listByStep($step, $staffId = 0, $dateTask = null, $dateEnd = null)
    {

        if($staffId == null) $staffId = 0;

        if($staffId > 0) {
            if(!$staff = $this->em->getRepository('App\Entity\Staff')->find($staffId)) {
                return array('message' => 'Staff person not founded');
            }
        } else {
          $staff = null;
        }
        $taskStaffs = $this->em->getRepository('App\Entity\TaskStaff')->findByStep($step, $staff, $dateTask, $dateEnd);
        $taskStaffsArray = array();
        foreach ($taskStaffs as $taskStaff) {
            $taskStaffsArray[] = $this->toArray($taskStaff);
        };
        return $taskStaffsArray;
    }

    /**
     * {@inheritdoc}
     */
    public function listByStepAll($step)
    {

        $taskStaffs = $this->em->getRepository('App\Entity\TaskStaff')->findByStepAll($step);
        $taskStaffsArray = array();
        foreach ($taskStaffs as $taskStaff) {
            $task = $taskStaff->getArrayData();
            if($taskStaff->getType() == null) {
              $type = "non classé";
            }  else {
              $type = $taskStaff->getType();
            }
            $taskStaffsArray[$type][] = $task;
        };
        return $taskStaffsArray;
    }


    public function listTaskLateLimit($step, $dateLimit, $from = null)
    {
        $taskStaffs = $this->em->getRepository('App\Entity\TaskStaff')->findWithLimit($step, $dateLimit, $from);
        $taskStaffsArray = array();
        foreach ($taskStaffs as $taskStaff) {
            $taskStaffsArray[] = $this->toArray($taskStaff);
        };
        return $taskStaffsArray;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(TaskStaff $object)
    {
        //Main data
        $objectArray = $object->toArray();

        //Gets related staff
        /*
        if (null !== $object->getSupervisor()) {
            $objectArray['supervisor'] = $this->staffService->toArray($object->getSupervisor());
        }


        //Gets related staff
        if (null !== $object->getStaff() && !$object->getStaff()->getSuppressed()) {
            $objectArray['staff'] = $this->staffService->toArray($object->getStaff());
        }*/
        //Gets related task
        if (null !== $object->getTask()) {
            $objectArray['task'] = $object->getTask()->toArray();
        }
        return $objectArray;
    }

}
