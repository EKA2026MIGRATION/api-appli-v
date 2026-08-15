<?php

namespace App\Controller;

use App\Entity\GroupActivity;
use App\Entity\Staff;
use App\Form\GroupActivityType;
use App\Service\GroupActivityServiceInterface;
use App\Service\StaffServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * GroupActivityController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class GroupActivityController extends AbstractController
{
    private $groupActivityService;

    private $staffService;

    public function __construct(
        GroupActivityServiceInterface $groupActivityService,
        StaffServiceInterface $staffService
    )
    {
        $this->groupActivityService = $groupActivityService;
        $this->staffService = $staffService;
    }

//LIST BY DATE
    /**
     * Lists all the groupActivities for a specific date
     *
     * @Route("/group-activity/list/{date}",
     *    name="group_activitylist_date",
     *    requirements={"date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=GroupActivity::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the groupActivity (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     *     default="1",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     *     default="50",
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function listByDate(Request $request, PaginatorInterface $paginator, $date)
    {
        $this->denyAccessUnlessGranted('groupActivityList');

        ini_set("memory_limit","1024M");

        $groupActivities = $this->groupActivityService->findAllByDate($date);

        $groupActivitiesArray = array();
        foreach ($groupActivities as $groupActivity) {
            $groupActivitiesArray[] = $this->groupActivityService->toArray($groupActivity);
        };

        return new JsonResponse($groupActivitiesArray);
    }



//LIST BY DATE AND BETWEEN
    /**
     * Lists all the groupActivities for a specific date
     *
     * @Route("/group-activity/tv-list/{date}/{from}/{to}",
     *    name="group_activity_tv_list_date",
     *    requirements={"date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=GroupActivity::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the groupActivity (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     *     default="1",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     *     default="50",
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function listByDateBetween(Request $request, $date, $from, $to)
    {
        $this->denyAccessUnlessGranted('groupActivityList');

        ini_set("memory_limit","1024M");

        $groupActivitiesArray = $this->groupActivityService->findByDateBetween($date, $from, $to);

        return new JsonResponse($groupActivitiesArray);
    }



//DUPLICATE GROUP ACTIVITY FROM DAY TO ANOTHER DAY
    /**
     * Duplicates all groups activity from a day to another day
     *
     * @Route("/group-activity/duplicate/{source}/{target}",
     *    name="group-activity_duplicate",
     *    requirements={"source": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=GroupActivity::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="source",
     *     in="path",
     *     description="Source for the ride (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function duplicate($source, $target)
    {

        $message = $this->groupActivityService->duplicateRecursive($source, $target);

        return new JsonResponse($message);
    }





// DUPLICATE
    /**
     * duplicate moment on same day to another hour
     *
     * @Route("/group-activity/duplicateMoment",
     *    name="group_duplicate_moment",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="groupActivity", ref=@Model(type=GroupActivity::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the groupActivity",
     *     required=true,
     *     @Model(type=GroupActivityType::class)
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function duplicateMoment(Request $request)
    {
        $createdData = $this->groupActivityService->duplicateMoment($request->getContent());
        return new JsonResponse($createdData);

    }

  /**
     * search by criterias
     *
     * @Route("/group-activity/search/criterias",
     *    name="group",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="groupActivity", ref=@Model(type=GroupActivity::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the groupActivity",
     *     required=true,
     *     @Model(type=GroupActivityType::class)
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function searchByCriterias(Request $request)
    {
        $createdData = $this->groupActivityService->searchByCriterias($request->getContent());
        return new JsonResponse($createdData);

    }




    //LIST BY LUNCH AND DATE
        /**
         * Lists all the pickup as lung group
         *
         * @Route("/group-activity/lunch/{date}",
         *    name="group_activity_list_lunch",
         *    requirements={
         *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
         *    },
         *    methods={"HEAD", "GET"})
         *
         * @SWG\Response(
         *     response=200,
         *     description="Success",
         *     @SWG\Schema(
         *         type="array",
         *         @SWG\Items(ref=@Model(type=GroupActivity::class))
         *     )
         * )
         * @SWG\Response(
         *     response=403,
         *     description="Access denied",
         * )
         * @SWG\Parameter(
         *     name="date",
         *     in="path",
         *     description="Date for the pickupActivity (YYYY-MM-DD | YYYY-MM)",
         *     type="string",
         * )
         * @SWG\Tag(name="GroupActivity")
         */
        public function listByLunchgroup(Request $request, $date)
        {
          //  $this->denyAccessUnlessGranted('pickupActivityList');

            $groups = $this->groupActivityService->listByLunchGroup($date);

            return new JsonResponse($groups);
        }



    //LIST BY DATE
    /**
     * Lists all the group by day
     *
     * @Route("/group-activity/listADay/{date}",
     *    name="group_activity_list_a_day",
     *    requirements={
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=GroupActivity::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the pickupActivity (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function listGroupADay(Request $request, $date)
    {

        $groups = $this->groupActivityService->getGroupADay($date);

        return new JsonResponse($groups);
    }

    //LIST BY DATE
    /**
     * Lists all the group by day
     *
     * @Route("/group-activity/today/",
     *    name="group_activity_list_today",
     *    requirements={
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=GroupActivity::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the pickupActivity (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function getGroupTodaya(Request $request)
    {

        $groups = $this->groupActivityService->getGroupToday();

        return new JsonResponse($groups);
    }


//DISPLAY BY DATE AND STAFFID
    /**
     * Displays the groupActivity for a specific date and staff
     *
     * @Route("/group-activity/display/{date}/{staffId}",
     *    name="group_activity_display_date_staff",
     *    requirements={
     *        "date": "^([0-9]{4}-[0-9]{2}-[0-9]{2})$",
     *        "staffId": "^([0-9]+)$"
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=GroupActivity::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the GroupActivity (YYYY-MM-DD)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="staffId",
     *     in="path",
     *     description="Id for the Staff",
     *     type="string",
     * )
     * @SWG\Tag(name="Ride")
     */
    public function displayByDateAndStaff($date, $staffId)
    {
        $this->denyAccessUnlessGranted('groupActivityDisplay');

        $groupActivitiesArray = array();
        $staff = $this->staffService->findOneById($staffId);
        if ($staff instanceof Staff && !$staff->getSuppressed()) {
            $groupActivities = $this->groupActivityService->findAllByDateByStaff($date, $staff);
            foreach ($groupActivities as $groupActivity) {
                $groupActivitiesArray[] = $this->groupActivityService->toArray($groupActivity);
            };
        }

        return new JsonResponse($groupActivitiesArray);
    }

//DISPLAY BY ID
    /**
     * Displays the groupActivity using its id
     *
     * @Route("/group-activity/display/{groupActivityId}",
     *    name="group_activitylist_id",
     *    requirements={"groupActivityId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("groupActivity", expr="repository.findOneById(groupActivityId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=GroupActivity::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Parameter(
     *     name="groupActivityId",
     *     in="path",
     *     description="Id for the groupActivity",
     *     type="string",
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function display(GroupActivity $groupActivity)
    {
        $this->denyAccessUnlessGranted('groupActivityDisplay', $groupActivity);

        $groupActivityArray = $this->groupActivityService->toArray($groupActivity);

        return new JsonResponse($groupActivityArray);
    }

//CREATE
    /**
     * Creates a GroupActivity
     *
     * @Route("/group-activity/create",
     *    name="group_activitycreate",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="groupActivity", ref=@Model(type=GroupActivity::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the GroupActivity",
     *     required=true,
     *     @Model(type=GroupActivityType::class)
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('groupActivityCreate');

        $createdData = $this->groupActivityService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//CREATE MULTIPLE
    /**
     * Creates multiples GroupActivitys
     *
     * @Route("/group-activity/create-multiple",
     *    name="group_activitycreate_multiple",
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
     *     description="Data for the GroupActivitys",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=GroupActivityType::class))
     *     )
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function createMultiple(Request $request)
    {
        $this->denyAccessUnlessGranted('groupActivityCreate');

        $createdData = $this->groupActivityService->createMultiple($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies groupActivity
     *
     * @Route("/group-activity/modify/{groupActivityId}",
     *    name="group_activitymodify",
     *    requirements={"groupActivityId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("groupActivity", expr="repository.findOneById(groupActivityId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="groupActivity", ref=@Model(type=GroupActivity::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Parameter(
     *     name="groupActivityId",
     *     in="path",
     *     required=true,
     *     description="Id of the groupActivity",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the GroupActivity",
     *     required=true,
     *     @Model(type=GroupActivityType::class)
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function modify(Request $request, GroupActivity $groupActivity)
    {
        $this->denyAccessUnlessGranted('groupActivityModify', $groupActivity);

        $modifiedData = $this->groupActivityService->modify($groupActivity, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes groupActivity and moves all the pickups as "Non pris en charge"
     *
     * @Route("/group-activity/delete/{groupActivityId}",
     *    name="group_activitydelete",
     *    requirements={"groupActivityId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("groupActivity", expr="repository.findOneById(groupActivityId)")
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
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Parameter(
     *     name="groupActivityId",
     *     in="path",
     *     required=true,
     *     description="Id of the groupActivity",
     *     type="integer",
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function delete(GroupActivity $groupActivity)
    {
        $this->denyAccessUnlessGranted('groupActivityDelete', $groupActivity);

        $suppressedData = $this->groupActivityService->delete($groupActivity);

        return new JsonResponse($suppressedData);
    }

    public function groupToday(GroupActivity $groupActivity)
    {

        $suppressedData = $this->groupActivityService->groupToday($groupActivity);
        return new JsonResponse($suppressedData);
    }


    /**
     * Deletes all groupActivity by date
     *
     * @Route("/group-activity/delete-all/{date}",
     *    name="group_activity_delete_all",
     *    requirements={"date": "^([0-9]{4}-[0-9]{2}-[0-9]{2})$"},
     *    methods={"HEAD", "DELETE"})
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
     *     name="date",
     *     in="path",
     *     required=true,
     *     description="Date for the groupActivity",
     *     type="string",
     * )
     * @SWG\Tag(name="GroupActivity")
     */
    public function deleteAllByDate($date)
    {
        $this->denyAccessUnlessGranted('groupActivityDelete');

        $suppressedData = $this->groupActivityService->deleteAllByDate($date);

        return new JsonResponse($suppressedData);
    }
}
