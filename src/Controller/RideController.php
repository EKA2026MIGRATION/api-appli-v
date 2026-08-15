<?php

namespace App\Controller;

use App\Entity\Ride;
use App\Entity\Staff;
use App\Form\RideType;
use App\Service\RideServiceInterface;
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
 * RideController class.
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RideController extends AbstractController
{
    private $staffService;

    private $rideService;

    public function __construct(
        RideServiceInterface $rideService,
        StaffServiceInterface $staffService
    ) {
        $this->rideService = $rideService;
        $this->staffService = $staffService;
    }

    //LIST

    /**
     * Lists all the rides coming or finished.
     *
     * @Route("/ride/list/{status}",
     *    name="ride_list",
     *    requirements={"status": "^(coming|finished)$"},
     *    defaults={"status": "coming"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ride::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="coming|finished rides",
     *     type="string",
     *     default="coming",
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
     * @SWG\Tag(name="Ride")
     */
    public function listAllByStatus(Request $request, PaginatorInterface $paginator, $status)
    {
        $this->denyAccessUnlessGranted('rideList');

        $rides = $paginator->paginate(
            $this->rideService->findAllByStatus($status),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $ridesArray = array();
        foreach ($rides->getItems() as $ride) {
            $ridesArray[] = $this->rideService->toArray($ride);
        }

        return new JsonResponse($ridesArray);
    }

    //LIST BY DATE FROM TO

    /**
     * Lists all the rides for tv-list for a specific date.
     *
     * @Route("/ride/tv-list/{date}/{from}/{to}",
     *    name="ride_tv_list",
     *    requirements={"date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ride::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the ride (YYYY-MM-DD | YYYY-MM)",
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
     * @SWG\Tag(name="Ride")
     */
    public function listByDateFromTo(Request $request, $date, $from, $to)
    {
        $ridesArray = $this->rideService->findRideTvList($date, $from, $to);

        return new JsonResponse($ridesArray);
    }

    //LIST BY DATE

    /**
     * Lists all the rides for a specific date.
     *
     * @Route("/ride/list/{date}",
     *    name="ride_list_date",
     *    requirements={"date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ride::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the ride (YYYY-MM-DD | YYYY-MM)",
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
     * @SWG\Tag(name="Ride")
     */
    public function listByDate(Request $request, PaginatorInterface $paginator, $date)
    {
        ini_set('memory_limit', '4096M');

        $this->denyAccessUnlessGranted('rideList');

        $rides = $this->rideService->findAllByDate($date);

        $ridesArray = array();
        foreach ($rides as $ride) {
            $ridesArray[] = $this->rideService->toArray($ride);
        }

        return new JsonResponse($ridesArray);
    }

    //LIST BY DATE

    /**
     * Lists all the rides for a specific date.
     *
     * @Route("/ride/realtime/{date}/{kind}/{moment}",
     *    name="ride_realtime_list_date",
     *    requirements={"date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ride::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the ride (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Tag(name="Ride")
     */
    public function listRealTime(Request $request, $date, $kind, $moment)
    {
        ini_set('memory_limit', '512M');

        $this->denyAccessUnlessGranted('rideList');

        $rides = $this->rideService->findRealtime($date, $kind, $moment);

        $ridesArray = array();
        foreach ($rides as $ride) {
            $ridesArray[] = $this->rideService->toArray($ride);
        }

        return new JsonResponse($ridesArray);
    }

    //DUPLICATE RIDE FROM DAY TO ANOTHER DAY

    /**
     * Duplicates all rides from a day to another day.
     *
     * @Route("/ride/duplicate/{source}/{target}",
     *    name="ride_duplicate",
     *    requirements={"source": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ride::class))
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
     * @SWG\Tag(name="Ride")
     */
    public function duplicate($source, $target)
    {
        $this->denyAccessUnlessGranted('rideList');

        $message = $this->rideService->duplicateRecursive($source, $target);

        return new JsonResponse($message);
    }

    //DISPLAY BY DATE AND STAFFID

    /**
     * Displays the rides for a specific date and staff.
     *
     * @Route("/ride/retrieve-group-activity/{date}/{staffId}/{kind}",
     *    name="ride_retrieve-group-activity_date_staff",
     *    requirements={
     *        "date": "^([0-9]{4}-[0-9]{2}-[0-9]{2})$",
     *        "staffId": "^([0-9]+)$",
     *        "kind" : "^(dropin|dropoff)$",
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ride::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the ride (YYYY-MM-DD)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="staffId",
     *     in="path",
     *     description="Id for the Staff",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="kind",
     *     in="path",
     *     description="type of ride dropin or dropoff",
     *     type="string",
     * )
     * @SWG\Tag(name="Ride")
     */
    public function retrieveGroupActivityByDateAndStaff($date, $staffId, $kind)
    {
        // $this->denyAccessUnlessGranted('rideDisplay');
        if (!$staff = $this->staffService->findOneById($staffId)) {
            return new JsonResponse(['status' => 'error: staff doesn\'t exist']);
        }

        $childsArray = $this->rideService->retrieveGroupActivity($staff, $date, $kind);

        return new JsonResponse($childsArray);
    }

    //DISPLAY BY DATE AND STAFFID

    /**
     * Displays the rides for a specific date and staff.
     *
     * @Route("/ride/display/{date}/{staffId}",
     *    name="ride_display_date_staff",
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
     *         @SWG\Items(ref=@Model(type=Ride::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the ride (YYYY-MM-DD)",
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
        // $this->denyAccessUnlessGranted('rideDisplay');
        ini_set('memory_limit', '1024M');

        $ridesArray = array();
        $staff = $this->staffService->findOneById($staffId);
        if ($staff instanceof Staff && !$staff->getSuppressed()) {
            $rides = $this->rideService->findAllByDateByStaff($date, $staff);
            foreach ($rides as $ride) {
                $ridesArray[] = $this->rideService->toArray($ride);
            }
        }

        return new JsonResponse($ridesArray);
    }

    //DISPLAY BY ID

    /**
     * Displays the ride using its id.
     *
     * @Route("/ride/display/{rideId}",
     *    name="ride_list_id",
     *    requirements={"rideId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("ride", expr="repository.findOneById(rideId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ride::class))
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
     *     name="rideId",
     *     in="path",
     *     description="Id for the ride",
     *     type="string",
     * )
     * @SWG\Tag(name="Ride")
     */
    public function display(Ride $ride)
    {
        // $this->denyAccessUnlessGranted('rideDisplay', $ride);

        $rideArray = $this->rideService->toArray($ride);

        return new JsonResponse($rideArray);
    }
    

    //CREATE

    /**
     * Creates a Ride.
     *
     * @Route("/ride/create",
     *    name="ride_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="ride", ref=@Model(type=Ride::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Ride",
     *     required=true,
     *     @Model(type=RideType::class)
     * )
     * @SWG\Tag(name="Ride")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('rideCreate');

        $createdData = $this->rideService->create($request->getContent());

        return new JsonResponse($createdData);
    }



    //update report

    /**
     * Update a Ride rert.
     *
     * @Route("/ride/report/update",
     *    name="ride_report_update",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="ride", ref=@Model(type=Ride::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Ride",
     *     required=true,
     *     @Model(type=RideType::class)
     * )
     * @SWG\Tag(name="Ride")
     */
    public function updateReport(Request $request)
    {

        $createdData = $this->rideService->updateReport($request->getContent());

        return new JsonResponse($createdData);
    }





    //CREATE MULTIPLE

    /**
     * Creates multiples Rides.
     *
     * @Route("/ride/create-multiple",
     *    name="ride_create_multiple",
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
     *     description="Data for the Rides",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=RideType::class))
     *     )
     * )
     * @SWG\Tag(name="Ride")
     */
    public function createMultiple(Request $request)
    {
        $this->denyAccessUnlessGranted('rideCreate');

        $createdData = $this->rideService->createMultiple($request->getContent());

        return new JsonResponse($createdData);
    }



    //CREATE DROPOFF




    //MODIFY

    /**
     * Modifies ride.
     *
     * @Route("/ride/modify/{rideId}",
     *    name="ride_modify",
     *    requirements={"rideId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("ride", expr="repository.findOneById(rideId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="ride", ref=@Model(type=Ride::class)),
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
     *     name="rideId",
     *     in="path",
     *     required=true,
     *     description="Id of the ride",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Ride",
     *     required=true,
     *     @Model(type=RideType::class)
     * )
     * @SWG\Tag(name="Ride")
     */
    public function modify(Request $request, Ride $ride)
    {
        $this->denyAccessUnlessGranted('rideModify', $ride);

        $modifiedData = $this->rideService->modify($ride, $request->getContent());

        return new JsonResponse($modifiedData);
    }

    //DELETE

    /**
     * Deletes ride and moves all the pickups as "Non pris en charge".
     *
     * @Route("/ride/delete/{rideId}",
     *    name="ride_delete",
     *    requirements={"rideId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("ride", expr="repository.findOneById(rideId)")
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
     *     name="rideId",
     *     in="path",
     *     required=true,
     *     description="Id of the ride",
     *     type="integer",
     * )
     * @SWG\Tag(name="Ride")
     */
    public function delete(Ride $ride)
    {
        $this->denyAccessUnlessGranted('rideDelete', $ride);

        $suppressedData = $this->rideService->delete($ride);

        return new JsonResponse($suppressedData);
    }
}
