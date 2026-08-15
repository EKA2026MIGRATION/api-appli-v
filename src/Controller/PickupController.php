<?php

namespace App\Controller;

use App\Entity\Pickup;
use App\Entity\Ride;
use App\Form\PickupType;
use App\Service\PickupServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * PickupController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupController extends AbstractController
{
    private $pickupService;

    public function __construct(PickupServiceInterface $pickupService)
    {
        $this->pickupService = $pickupService;
    }

//LIST BY STATUS AND DATE
    /**
     * Lists all the pickups by date and status
     *
     * @Route("/pickup/list/{date}/{status}",
     *    name="pickup_list_status",
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
     *         @SWG\Items(ref=@Model(type=Pickup::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the pickup (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="Status for the Pickup automatic|absent|supported|null",
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
     * @SWG\Tag(name="Pickup")
     */
    public function listByStatus(Request $request, PaginatorInterface $paginator, $date, $status)
    {
        $this->denyAccessUnlessGranted('pickupList');

        $pickups = $paginator->paginate(
            $this->pickupService->findAllByStatus($date, $status),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 500)
        );

        $pickupsArray = array();
        foreach ($pickups->getItems() as $pickup) {
            $pickupsArray[] = $this->pickupService->toArray($pickup);
        };

        return new JsonResponse($pickupsArray);
    }



//DISPLAY
    /**
     * Displays pickup
     *
     * @Route("/pickup/listByChildId/{childId}/{from}/{to}",
     *    name="pickup_listByChildId",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Pickup::class))
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
     * @SWG\Tag(name="Pickup")
     */
    public function retrieveByChildId($childId, $from, $to)
    {
       // $this->denyAccessUnlessGranted('pickupDisplay', $pickup);

        $result = $this->pickupService->retrieveByChildId($childId, $from, $to);

        return new JsonResponse($result);
    }








//LIST  PICKUP WEEK
    /**
     * Lists all the pickups by date and status for a week
     *
     * @Route("/pickup/listWeek/{monday}",
     *    name="pickup_list_week",
     *    requirements={
     *        "monday": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
     *    },
     *    defaults={"status": "null"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Pickup::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="monday",
     *     in="path",
     *     description="Date for the pickup (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="Status for the Pickup automatic|absent|supported|null",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function listWeekStatus(Request $request, $monday)
    {
        $this->denyAccessUnlessGranted('pickupList');

        $pickupsArray = $this->pickupService->listByWeek($monday);
    

        return new JsonResponse($pickupsArray);
    }



//LIST NOT AFFECTED
    /**
     * Lists all the pickups by date not affected to a ride
     *
     * @Route("/pickup/list/{date}/unaffected/{kind}",
     *    name="pickup_list_unaffected",
     *    requirements={
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
     *        "kind": "^(dropin|dropoff)$"
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Pickup::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the pickup (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="kind",
     *     in="path",
     *     description="Kind of Pickup (dropin|dropoff)",
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
     * @SWG\Tag(name="Pickup")
     */
    public function listUnaffected(Request $request, PaginatorInterface $paginator, $date, $kind)
    {
        $this->denyAccessUnlessGranted('pickupList');

        $pickups = $paginator->paginate(
            $this->pickupService->findAllUnaffected($date, $kind),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 500)
        );

        $pickupsArray = array();
        foreach ($pickups->getItems() as $pickup) {
            $pickupsArray[] = $this->pickupService->toArray($pickup);
        };

        return new JsonResponse($pickupsArray);
    }

//AFFECT
    /**
     * Affects all the Pickups to the rides
     *
     * @Route("/pickup/affect/{date}/{kind}/{force}",
     *    name="pickup_affect",
     *    requirements={
     *        "date": "^([0-9]{4}-[0-9]{2}-[0-9]{2})$",
     *        "kind": "^(all|dropin|dropoff)$",
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
     *     description="Date for the pickups (YYYY-MM-DD)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="kind",
     *     in="path",
     *     description="Kind of Pickup (all|dropin|dropoff)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="force",
     *     in="path",
     *     description="To force the rewrite of pickups (true|false(default))",
     *     type="boolean",
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function affect($date, $kind, bool $force)
    {
        $this->denyAccessUnlessGranted('pickupModify');

        $this->pickupService->affect($date, $kind, $force);

        return new JsonResponse(array('status' => true));
    }

//AFFECT TO LINKED RIDE
    /**
     * Affects all the Pickups to the linked Ride
     *
     * @Route("/pickup/affect-linked-ride/{rideId}/{rideLinkedId}",
     *    name="pickup_affect_linked_ride",
     *    requirements={"rideId": "^([0-9]+)$"},
     *    defaults={"rideLinkedId": "null"},
     *    methods={"HEAD", "PUT"})
     * @Entity("ride", expr="repository.findOneById(rideId)")
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
     *     name="rideId",
     *     in="path",
     *     description="Id of the current Ride (dropin one)",
     *     type="string",
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function affectLinkedRide(Ride $ride, $rideLinkedId = null)
    {
        $this->denyAccessUnlessGranted('pickupModify');

        $this->pickupService->affectPickupLinkedRide($ride, $rideLinkedId);

        return new JsonResponse(array('status' => true));
    }

    /**
     * Creates multiples pickups dropoff from ride dropin
     *
     * @Route("/pickup/multiple-affect-pickup-linked-ride",
     *    name="pickup-multiple-affect-pickup-linked-ride",
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
     *     description="Data for the Pickups",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=PickupType::class))
     *     )
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function MultipleAffectPickupLinkedRide(Request $request)
    {
        $this->denyAccessUnlessGranted('pickupModify');

        $createdData = $this->pickupService->MultipleAffectPickupLinkedRide($request->getContent());

        return new JsonResponse($createdData);
    }

//UNAFFECT
    /**
     * Unaffects all the Pickups to the rides
     *
     * @Route("/pickup/unaffect/{date}/{kind}",
     *    name="pickup_unaffect",
     *    requirements={
     *        "date": "^([0-9]{4}-[0-9]{2}-[0-9]{2})$",
     *        "kind": "^(all|dropin|dropoff)$"
     *    },
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
     * @SWG\Parameter(
     *     name="kind",
     *     in="path",
     *     description="Kind of Pickup (all|dropin|dropoff)",
     *     type="string",
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function unaffect($date, $kind)
    {
        $this->denyAccessUnlessGranted('pickupModify');

        $this->pickupService->unaffect($date, $kind);

        return new JsonResponse(array('status' => true));
    }

//DISPATCH
    /**
     * Modifies the dispatch for Pickups
     *
     * @Route("/pickup/dispatch",
     *    name="pickup_dispatch",
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
     *     name="data",
     *     in="body",
     *     description="Data for the dispatch",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *              @SWG\Property(property="pickupId", type="integer"),
     *              @SWG\Property(property="rideId", type="integer"),
     *              @SWG\Property(property="sortOrder", type="integer"),
     *              @SWG\Property(property="validated", type="string"),
     *              @SWG\Property(property="start", type="string"))
     *     )
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function dispatch(Request $request)
    {
        $this->denyAccessUnlessGranted('pickupModify');

        $dispatchData = $this->pickupService->dispatch($request->getContent());

        return new JsonResponse($dispatchData);
    }

//DISPLAY
    /**
     * Displays pickup
     *
     * @Route("/pickup/display/{pickupId}",
     *    name="pickup_display",
     *    requirements={"pickupId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("pickup", expr="repository.findOneById(pickupId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Pickup::class))
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
     *     name="pickupId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickup",
     *     type="integer",
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function display(Pickup $pickup)
    {
        $this->denyAccessUnlessGranted('pickupDisplay', $pickup);

        $pickupArray = $this->pickupService->toArray($pickup);

        return new JsonResponse($pickupArray);
    }

//CREATE
    /**
     * Creates a Pickup
     *
     * @Route("/pickup/create",
     *    name="pickup_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="pickup", ref=@Model(type=Pickup::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Pickup",
     *     required=true,
     *     @Model(type=PickupType::class)
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('pickupCreate');

        $createdData = $this->pickupService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//CREATE MULTIPLE
    /**
     * Creates multiples Pickups
     *
     * @Route("/pickup/create-multiple",
     *    name="pickup_create_multiple",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="pickup", ref=@Model(type=Pickup::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Pickup",
     *     required=true,
     *     @Model(type=PickupType::class)
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function createMultiple(Request $request)
    {
        $this->denyAccessUnlessGranted('pickupCreate');

        $createdData = $this->pickupService->createMultiple($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies pickup
     *
     * @Route("/pickup/modify/{pickupId}/{sender}",
     *    requirements={"pickupId": "^([0-9]+)$"},
     *    defaults={"sender": "null"},
     *    methods={"HEAD", "PUT"})
     * @Entity("pickup", expr="repository.findOneById(pickupId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="pickup", ref=@Model(type=Pickup::class)),
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
     *     name="pickupId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickup",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Pickup",
     *     required=true,
     *     @Model(type=PickupType::class)
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function modify(Request $request, Pickup $pickup, $sender = null)
    {
        $this->denyAccessUnlessGranted('pickupModify', $pickup);

        $modifiedData = $this->pickupService->modify($pickup, $request->getContent(), $sender);

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes pickup
     *
     * @Route("/pickup/delete/{pickupId}",
     *    name="pickup_delete",
     *    requirements={"pickupId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("pickup", expr="repository.findOneById(pickupId)")
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
     *     name="pickupId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickup",
     *     type="integer",
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function delete(Pickup $pickup)
    {
        $this->denyAccessUnlessGranted('pickupDelete', $pickup);

        $suppressedData = $this->pickupService->delete($pickup);

        return new JsonResponse($suppressedData);
    }

//DELETE BY REGISTRATION_ID
    /**
     * Deletes pickup using the registrationId
     *
     * @Route("/pickup/delete-registration/{registrationId}",
     *    name="pickup_delete_by_registration",
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
     *     description="RegistrationId linked to the pickup",
     *     type="integer",
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function deleteByRegistrationId(int $registrationId)
    {
        $this->denyAccessUnlessGranted('pickupDelete');

        $suppressedData = $this->pickupService->deleteByRegistrationId($registrationId);

        return new JsonResponse($suppressedData);
    }





//LIST BY REGISTRATION_ID
    /**
     * list pickup using the registrationId
     *
     * @Route("/pickup/list-by-registration/{registrationId}",
     *    name="pickup_list_by_registration",
     *    requirements={"registrationId": "^([0-9]+)$"},
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
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Parameter(
     *     name="registrationId",
     *     in="path",
     *     required=true,
     *     description="RegistrationId linked to the pickup",
     *     type="integer",
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function listByRegistrationId(int $registrationId)
    {

        $pickups = $this->pickupService->listByRegistrationId($registrationId);

        return new JsonResponse($pickups);
    }




//GET LAST PEC
    /**
     * Get last PEC time changed by child_id
     *
     * @Route("/pickup/lastPEC/{childId}/{kind}",
     *    name="pickup_last_PEC",
     *    requirements={"childId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="pickup", ref=@Model(type=Pickup::class)),
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
     *     name="pickupId",
     *     in="path",
     *     required=true,
     *     description="Id of the pickup",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Pickup",
     *     required=true,
     *     @Model(type=PickupType::class)
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function lastPEC(Request $request, $childId, $kind)
    {
        //$this->denyAccessUnlessGranted('pickupLastPEC', $pickup);

        $datas = $this->pickupService->getLastPEC($childId, $kind);
        return new JsonResponse(array($datas));
    }



//UPDATE SMS SENT DATA
  /**
   * Update datas on sms sent on pickup
   *
   * @Route("/pickup/update-sms-sent-data",
   *    name="pickup_update_sms_sent_data",
   *    methods={"HEAD", "POST"})
   *
   * @SWG\Response(
   *     response=200,
   *     description="Success",
   *     @SWG\Schema(
   *         @SWG\Property(property="status", type="boolean"),
   *         @SWG\Property(property="message", type="string"),
   *         @SWG\Property(property="pickup", ref=@Model(type=Pickup::class)),
   *     )
   * )
   * @SWG\Response(
   *     response=403,
   *     description="Access denied",
   * )
   * @SWG\Parameter(
   *     name="data",
   *     in="body",
   *     description="Data for the sms sent data",
   *     required=true,
   *     @Model(type=PickupType::class)
   * )
   * @SWG\Tag(name="Pickup")
   */
  public function updateSmsSentData(Request $request)
  {
      //$this->denyAccessUnlessGranted('pickupCreate');



      $createdData = $this->pickupService->updateSmsSentData($request->getContent());

      return new JsonResponse($createdData);
  }






//GEOCODING
    /**
     * Geocodes all the Pickups
     *
     * @Route("/pickup/geocode",
     *    name="pickup_geocode",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="counter", type="integer"),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Pickup")
     */
    public function geocode()
    {
        set_time_limit(600);

        $this->denyAccessUnlessGranted('pickupGeocode');

        $counterRecords = $this->pickupService->geocode();

        return new JsonResponse(array('Number of records treated' => $counterRecords));
    }
}
