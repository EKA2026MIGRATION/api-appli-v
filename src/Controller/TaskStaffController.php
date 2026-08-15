<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskStaff;
use App\Form\TaskStaffType;
use App\Service\TaskStaffService;
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
 * TaskStaff class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class TaskStaffController extends AbstractController
{

    private $taskStaffService;

    public function __construct(TaskStaffService $taskStaffService)
    {
        $this->taskStaffService = $taskStaffService;
    }

//CREATE
    /**
     * Creates TaskStaff and return task by staff and date
     *
     * @Route("/task/staff/create",
     *    name="task_staff_create",
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
     *     description="Data for the TaskStaff",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
     *     )
     * )
     * @SWG\Tag(name="TaskStaff")
     */
    public function create(Request $request)
    {
        $data = $request->getContent();

        $taskStaffs = $this->taskStaffService->create($data);

        return new JsonResponse($taskStaffs) ;
    }



    //UPDATE
        /**
         * Update TaskStaff
         *
         * @Route("/task/staff/update",
         *    name="task_staff_update",
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
         *     description="Data for the TaskStaff",
         *     required=true,
         *     @SWG\Schema(
         *         type="array",
         *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
         *     )
         * )
         * @SWG\Tag(name="TaskStaff")
         */
        public function update(Request $request)
        {
            $data = $request->getContent();

            $taskStaff = $this->taskStaffService->update($data);

            return new JsonResponse($taskStaff) ;
        }






//UPDATE STEP
    /**
     * Update Step with target step
     *
     * @Route("/task/staff/modify/step",
     *    name="task_staff_modify_step",
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
     *     description="Data for the TaskStaff",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
     *     )
     * )
     * @SWG\Tag(name="TaskStaff")
     */
    public function modifyStep(Request $request)
    {
        $data = $request->getContent();

        $taskStaff = $this->taskStaffService->modifyStep($data);

        return new JsonResponse($taskStaff) ;
    }




    //CREATE
        /**
         * Delete TaskStaff
         *
         * @Route("/task/staff/delete",
         *    name="task_staff_delete",
         *    methods={"DELETE"})
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
         *     description="Delete for the TaskStaff",
         *     required=true,
         *     @SWG\Schema(
         *         type="array",
         *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
         *     )
         * )
         * @SWG\Tag(name="TaskStaff")
         */
        public function delete(Request $request)
        {
            $data = $request->getContent();
            $datas = $this->taskStaffService->delete($data);
            return new JsonResponse($datas) ;
        }


//RETRIEVE
    /**
     * Retrieve Task by User & date
     *
     * @Route("/task/staff/retrieve/{staffId}/{date}",
     *    name="task_staff_retrieve_by_date",
     *    methods={"HEAD", "GET"})
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
     *     description="Task by date and staff_id",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
     *     )
     * )
     * @SWG\Tag(name="TaskStaff")
     */
    public function retrieve(Request $request, $staffId, $date)
    {
        $taskStaffs = $this->taskStaffService->retrieveTasks($staffId, $date);
        $taskStaffsArray = array();

        foreach ($taskStaffs as $taskStaff) {
            $taskStaffsArray[] = $this->taskStaffService->toArray($taskStaff);
        };

        return new JsonResponse($taskStaffsArray);

    }


  //DISPLAY
        /**
         * Retrieve Task by taskStaffId
         *
         * @Route("/task/staff/display/{taskStaffId}",
         *    name="task_staff_display",
         *    methods={"HEAD", "GET"})
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
         *     description="Task by task_staff_id",
         *     required=true,
         *     @SWG\Schema(
         *         type="array",
         *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
         *     )
         * )
         * @SWG\Tag(name="TaskStaff")
         */
        public function display($taskStaffId,  EntityManagerInterface $em)
        {

            $taskStaff = $em->getRepository('App\Entity\TaskStaff')->find($taskStaffId);
            $taskStaffArray = $taskStaff->toArray();

            return new JsonResponse($taskStaffArray);

        }






    //LIST
        /**
         * Retrieve Task by date
         *
         * @Route("/task/staff/list/{date}",
         *    name="task_staff_by_date",
         *    methods={"HEAD", "GET"})
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
         *     description="Task by date",
         *     required=true,
         *     @SWG\Schema(
         *         type="array",
         *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
         *     )
         * )
         * @SWG\Tag(name="TaskStaff")
         */
        public function list(Request $request, $date)
        {
            $taskStaffs = $this->taskStaffService->list($date);
            $taskStaffsArray = array();

            foreach ($taskStaffs as $taskStaff) {
                $taskStaffsArray[] = $this->taskStaffService->toArray($taskStaff);
            };

            return new JsonResponse($taskStaffsArray);

        }

        //LIST STEP
            /**
             * Retrieve Task by step
             *
             * @Route("/task/staff/list/step/{step}/{staffId}/{dateTask}/{dateEnd}",
             *    name="task_staff_by_step",
             *    methods={"HEAD", "GET"})
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
             *     description="Task by step",
             *     required=true,
             *     @SWG\Schema(
             *         type="array",
             *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
             *     )
             * )
             * @SWG\Tag(name="TaskStaff")
             */
            public function listByStep(Request $request, $step, $staffId = 0, $dateTask = null, $dateEnd = null)
            {

                ini_set('memory_limit', '2048M');

                $taskStaffsArray = $this->taskStaffService->listByStep($step, $staffId, $dateTask, $dateEnd);
                return new JsonResponse($taskStaffsArray);

            }



            //LIST STEP
                /**
                 * Retrieve Task by step
                 *
                 * @Route("/task/staff/list/stepAll/{step}",
                 *    name="task_staff_by_step_all",
                 *    methods={"HEAD", "GET"})
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
                 *     description="Task by step",
                 *     required=true,
                 *     @SWG\Schema(
                 *         type="array",
                 *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
                 *     )
                 * )
                 * @SWG\Tag(name="TaskStaff")
                 */
                public function listByStepAll(Request $request, $step)
                {

                    ini_set('memory_limit', '4096M');
                    $taskStaffsArray = $this->taskStaffService->listByStepAll($step);
                    return new JsonResponse($taskStaffsArray);

                }







    //LIST LATE
        /**
         * Retrieve TASK LATE (change by step)
         *
         * @Route("/task/staff/late/limit/{step}/{dateLimit}/{from}",
         *    name="task_staff_late",
         *    methods={"HEAD", "GET"})
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
         *     description="Task by step",
         *     required=true,
         *     @SWG\Schema(
         *         type="array",
         *         @SWG\Items(ref=@Model(type=TaskStaffType::class))
         *     )
         * )
         * @SWG\Tag(name="TaskStaff")
         */
        public function listTaskLateLimit(Request $request, $step, $dateLimit, $from = null)
        {
            $taskStaffsArray = $this->taskStaffService->listTaskLateLimit($step, $dateLimit, $from);
            return new JsonResponse($taskStaffsArray);

        }


}
