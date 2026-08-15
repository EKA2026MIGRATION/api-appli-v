<?php

namespace App\Controller;

use App\Entity\PickupActivity;
use App\Form\PickupActivityType;
use App\Service\PickupActivityServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * PickupActivityController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupActivityController extends AbstractController
{
    private $pickupActivityService;

    public function __construct(PickupActivityServiceInterface $pickupActivityService)
    {
        $this->pickupActivityService = $pickupActivityService;
    }

//LIST BY STATUS AND DATE
    /**
     * Lists all the pickupActivity by date and status
     *
     * @Route("/pickup-activity/list/{date}/{status}",
     *    name="pickup_activity_list_status",
     *    requirements={
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
     *        "status": "^(automatic|absent|supported|null)$"
     *    },
     *    defaults={"status": "null"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PickupActivity::class))
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
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="Status for the PickupActivity automatic|absent|supported|null",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function listByStatus(Request $request, PaginatorInterface $paginator, $date, $status)
    {
        $this->denyAccessUnlessGranted('pickupActivityList');

        $pickupActivities = $paginator->paginate(
            $this->pickupActivityService->findAllByStatus($date, $status),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $pickupActivitiesArray = array();
        foreach ($pickupActivities->getItems() as $pickupActivity) {
            $pickupActivitiesArray[] = $this->pickupActivityService->toArray($pickupActivity, 'light');
        };

        return new JsonResponse($pickupActivitiesArray);
    }










//LIST BY DATE 
    /**
     * Lists all the pickupActivity by date
     *
     * @Route("/pickup-activity/listByChild/{date}",
     *    name="pickup_activity_list_bychild-bydate",
     *    requirements={
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
     *    },
     *    defaults={"status": "null"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PickupActivity::class))
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
     * @SWG\Tag(name="PickupActivity")
     */
    public function listByChild(Request $request, $date)
    {
        $this->denyAccessUnlessGranted('pickupActivityList');
    
        $pickupActivitiesArray[] = $this->pickupActivityService->listByChild($date);

        return new JsonResponse($pickupActivitiesArray);
    }


    //LIST BY DATE
    /**
     * Lists all the pickupActivity by date
     *
     * @Route("/pickup-activity/listPickupaday/{date}",
     *    name="pickup_activity_list_pickup_a_day",
     *    requirements={
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
     *    },
     *    defaults={"status": "null"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PickupActivity::class))
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
     * @SWG\Tag(name="PickupActivity")
     */
    public function listPickupADay(Request $request, $date)
    {
        $this->denyAccessUnlessGranted('pickupActivityList');

        $pickupActivitiesArray = $this->pickupActivityService->getPickupsADay($date);

        return new JsonResponse($pickupActivitiesArray);
    }




//LIST BY ChildId AND DATE
    /**
     * Lists all the pickupActivity by childId and date
     *
     * @Route("/pickup-activity/list/{childId}/{date}",
     *    name="pickup_activity_list_child",
     *    requirements={
     *        "childId": "^([0-9]+)$",
     *        "date": "^([0-9]{4}-[0-9]{2}-[0-9]{2})$"
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PickupActivity::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="childId",
     *     in="path",
     *     required=true,
     *     description="Id of the child",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the pickupActivity (YYYY-MM-DD)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function listByChildDate(Request $request, PaginatorInterface $paginator, $date, $childId)
    {
        $this->denyAccessUnlessGranted('pickupActivityList');

        $pickupActivities = $paginator->paginate(
            $this->pickupActivityService->findAllByChildDate($childId, $date),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $pickupActivitiesArray = array();
        foreach ($pickupActivities->getItems() as $pickupActivity) {
            $pickupActivitiesArray[] = $this->pickupActivityService->toArray($pickupActivity);
        };

        return new JsonResponse($pickupActivitiesArray);
    }

//AFFECT
    /**
     * Affects all the Pickups to the GroupActivity
     *
     * @Route("/pickup-activity/affect/{date}/{force}",
     *    name="pickup_activity_affect",
     *    requirements={
     *        "date": "^([0-9]{4}-[0-9]{2}-[0-9]{2})$",
     *        "force": "^(true|false)$"
     *    },
     *    defaults={"force": false},
     *    methods={"HEAD", "PUT"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the pickups Activity (YYYY-MM-DD)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="force",
     *     in="path",
     *     description="To force the rewrite of pickups Activity (true|false(default))",
     *     type="boolean",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function affect($date, bool $force)
    {
        set_time_limit(600);

        $this->denyAccessUnlessGranted('pickupActivityModify');

        $result = $this->pickupActivityService->affect($date, $force);

        return new JsonResponse($result);

        return new JsonResponse(array('status' => true));
    }

//UNAFFECT
    /**
     * Unaffects all the Pickups to the GroupActivity
     *
     * @Route("/pickup-activity/unaffect/{date}",
     *    name="pickup_activity_unaffect",
     *    requirements={"date": "^([0-9]{4}-[0-9]{2}-[0-9]{2})$"},
     *    methods={"HEAD", "PUT"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the pickups (YYYY-MM-DD)",
     *     type="string",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function unaffect($date)
    {
        $this->denyAccessUnlessGranted('pickupActivityModify');

        $this->pickupActivityService->unaffect($date);

        return new JsonResponse(array('status' => true));
    }

//DISPLAY
    /**
     * Displays pickupActivity
     *
     * @Route("/pickup-activity/display/{pickupActivityId}",
     *    name="pickup_activity_display",
     *    requirements={"pickupActivityId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("pickupActivity", expr="repository.findOneById(pickupActivityId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PickupActivity::class))
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
     *     name="pickupActivityId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickupActivity",
     *     type="integer",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function display(PickupActivity $pickupActivity)
    {
        $this->denyAccessUnlessGranted('pickupActivityDisplay', $pickupActivity);

        $pickupActivityArray = $this->pickupActivityService->toArray($pickupActivity);

        return new JsonResponse($pickupActivityArray);
    }


    /**
     * Displays pickupActivity
     *
     * @Route("/pickup-activity/addTogroup/{pickupActivityId}/{groupId}",
     *    name="pickup_activity_add_to_group",
     *    requirements={"pickupActivityId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PickupActivity::class))
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
     *     name="pickupActivityId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickupActivity",
     *     type="integer",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function addPickupActivityTogroup($pickupActivityId, $groupId) {


        $pickupActivityArray = $this->pickupActivityService->addActivityToGroup($pickupActivityId, $groupId);

        return new JsonResponse($pickupActivityArray);
    }



    /**
     * Remove pickupActivity
     *
     * @Route("/pickup-activity/removeFromGroup/{groupId}/{childId}",
     *    name="pickup_activity_remove_child_from_group",
     *    requirements={"groupId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PickupActivity::class))
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
     *     name="pickupActivityId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickupActivity",
     *     type="integer",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function removeChildFromGroup($groupId, $childId) {


        $pickupActivityArray = $this->pickupActivityService->removeChildFromGroup($groupId, $childId);

        return new JsonResponse($pickupActivityArray);
    }


     /**
     * Displays pickupActivity
     *
     * @Route("/pickup-activity/associatedByRegistration/{pickupActivityId}",
     *    name="pickup_activity_associated_by_registration",
     *    requirements={"pickupActivityId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("pickupActivity", expr="repository.findOneById(pickupActivityId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PickupActivity::class))
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
     *     name="pickupActivityId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickupActivity",
     *     type="integer",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function associatedByRegistration(PickupActivity $pickupActivity) {

        $this->denyAccessUnlessGranted('pickupActivityDisplay', $pickupActivity);

        $pickupActivityArray = $this->pickupActivityService->associatedByRegistration($pickupActivity);

        return new JsonResponse($pickupActivityArray);
    }


//CREATE
    /**
     * Creates a PickupActivity
     *
     * @Route("/pickup-activity/create",
     *    name="pickup_activity_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="pickupActivity", ref=@Model(type=PickupActivity::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the PickupActivity",
     *     required=true,
     *     @Model(type=PickupActivityType::class)
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('pickupActivityCreate');

        $createdData = $this->pickupActivityService->create($request->getContent());

        return new JsonResponse($createdData);
    }


//CREATE
    /**
     * Creates a PickupActivity
     *
     * @Route("/pickup-activity/updateAllRegistration",
     *    name="pickup_activity_update_all_registration",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="pickupActivity", ref=@Model(type=PickupActivity::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the PickupActivity",
     *     required=true,
     *     @Model(type=PickupActivityType::class)
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function updateAllRegistration(Request $request)
    {
        $this->denyAccessUnlessGranted('pickupActivityCreate');

        $createdData = $this->pickupActivityService->updateAllRegistration($request->getContent());

        return new JsonResponse($createdData);
    }

//CREATE MULTIPLE
    /**
     * Creates multiples PickupActivities
     *
     * @Route("/pickup-activity/create-multiple",
     *    name="pickup_activity_create_multiple",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="pickupActivity", ref=@Model(type=PickupActivity::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the PickupActivity",
     *     required=true,
     *     @Model(type=PickupActivityType::class)
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function createMultiple(Request $request)
    {
        $this->denyAccessUnlessGranted('pickupActivityCreate');

        $createdData = $this->pickupActivityService->createMultiple($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies pickupActivity
     *
     * @Route("/pickup-activity/modify/{pickupActivityId}",
     *    name="pickup_activity_modify",
     *    requirements={"pickupActivityId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("pickupActivity", expr="repository.findOneById(pickupActivityId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="pickupActivity", ref=@Model(type=PickupActivity::class)),
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
     *     name="pickupActivityId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickupActivity",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the PickupActivity",
     *     required=true,
     *     @Model(type=PickupActivityType::class)
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function modify(Request $request, PickupActivity $pickupActivity)
    {
        $this->denyAccessUnlessGranted('pickupActivityModify', $pickupActivity);

        $modifiedData = $this->pickupActivityService->modify($pickupActivity, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes pickupActivity
     *
     * @Route("/pickup-activity/delete/{pickupActivityId}",
     *    name="pickup_activity_delete",
     *    requirements={"pickupActivityId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("pickupActivity", expr="repository.findOneById(pickupActivityId)")
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
     *     name="pickupActivityId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickupActivity",
     *     type="integer",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function delete(PickupActivity $pickupActivity)
    {
        $this->denyAccessUnlessGranted('pickupActivityDelete', $pickupActivity);

        $suppressedData = $this->pickupActivityService->delete($pickupActivity);

        return new JsonResponse($suppressedData);
    }

//DELETE BY REGISTRATION_ID
    /**
     * Deletes pickupActivity using the registrationId
     *
     * @Route("/pickup-activity/delete-registration/{registrationId}",
     *    name="pickup_activity_delete_by_registration",
     *    requirements={"registrationId": "^([0-9]+)$"},
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
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Parameter(
     *     name="registrationId",
     *     in="path",
     *     required=true,
     *     description="RegistrationId linked to the pickupActivity",
     *     type="integer",
     * )
     * @SWG\Tag(name="PickupActivity")
     */
    public function deleteByRegistrationId(int $registrationId)
    {
        $this->denyAccessUnlessGranted('pickupActivityDelete');

        $suppressedData = $this->pickupActivityService->deleteByRegistrationId($registrationId);

        return new JsonResponse($suppressedData);
    }
}
