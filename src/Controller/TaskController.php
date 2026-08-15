<?php

namespace App\Controller;

use App\Entity\Task;

use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Task class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class TaskController extends AbstractController
{
//LIST
    /**
     * Lists all the task
     *
     * @Route("/task/list",
     *    name="task_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Task::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Task")
     */
    public function list(Request $request, EntityManagerInterface $em)
    {
        //$this->denyAccessUnlessGranted('taskList');
        $tasks = $em->getRepository('App\Entity\Task')->findBy(['isActive' => 1]);

        $arr = [];

       foreach($tasks as $task) {
         $arr[$task->getMoment()][$task->getId()] = $task->getName();
       }
        return new JsonResponse($arr);
  }

//LIST
    /**
     * Lists all the task done a day
     *
     * @Route("/task/list/done/{date}",
     *    name="task_list_date",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Task::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Task")
     */
    public function listDone(Request $request, EntityManagerInterface $em, $date = null)
    {

        $taskdones = $em->getRepository('App\Entity\TaskStaff')->findTaskDone($date);

        $taskDoneArray = [];

        foreach($taskdones as $taskdone) {
            $person = $taskdone->getStaff()->getPerson();
            $taskDoneArray[$taskdone->getName()][] = $person->getFirstname().' '.$person->getLastname();
        }

        $tasks = $em->getRepository('App\Entity\Task')->findBy(
                                                            ['isActive' => 1],
                                                            [
                                                              'moment' => 'DESC',
                                                              'name' => 'ASC'
                                                            ]
                                                          );
        $arr = [];
       foreach($tasks as $task) {

         if(key_exists($task->getName(), $taskDoneArray)) {
            $listStaffName = implode(', ', $taskDoneArray[$task->getName()]);
            $status = 1;

         } else {
           $listStaffName = null;
           $status = 0;
         }

         $arr[$task->getMoment()][$task->getId()] = [
                                                        'status' => $status,
                                                        'staff' => $listStaffName,
                                                        'name' => $task->getName(),

                                                    ];
       }
        return new JsonResponse($arr);
  }




//DELETE BASIC TASK
    /**
     * switch to inactive a basic task
     *
     * @Route("/task/deleteBasicTask",
     *    name="task_delete_basictask",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Task",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Task::class))
     *     )
     * )
     * @SWG\Tag(name="Task")
     */
    public function deleteBasicTask(Request $request, EntityManagerInterface $em)
    {

      $data = $request->getContent();
      $values = json_decode($data, true);

      $task = $em->getRepository('App\Entity\Task')->find($values['taskId']);

      $task->setIsActive(0);

      $em->persist($task);
      $em->flush();

      return new JsonResponse(['message' => 'tache supprimée']) ;
    }



//ADD BASIC TASK
    /**
     * ADD A a basic task
     *
     * @Route("/task/addBasicTask/",
     *    name="task_add_basictask",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Task",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Task::class))
     *     )
     * )
     * @SWG\Tag(name="Task")
     */
    public function addBasicTask(Request $request, EntityManagerInterface $em)
    {
      $data = $request->getContent();

      $values = json_decode($data, true);

      $task = new Task();
      $task->setName($values['task_name']);
      $task->setMoment($values['moment']);
      $task->setIsActive(1);

      $em->persist($task);
      $em->flush();


      return new JsonResponse(['message' => 'tache ajoutée']) ;
    }


}
