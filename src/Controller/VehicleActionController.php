<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\VehicleAction;
use App\Form\VehicleType;
use App\Entity\VehicleCheckup;
use App\Service\VehicleServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * VehicleActionController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleActionController extends AbstractController
{
    private $vehicleService;

    public function __construct(VehicleServiceInterface $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

//ADD
    /**
     * Add action to a Vehicle
     *
     * @Route("/vehicle/add/action",
     *    name="vehicle_add_action",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="vehicle", ref=@Model(type=VehicleAction::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="VehicleAction")
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $createdData = $this->vehicleService->addAction($request->getContent());

        return new JsonResponse($createdData);
    }


// ajouter une plage de date > moyenne

// ajout de la photo du reçue de l'essence


//LIST BY VEHICLE
    /**
     * Lists all the action by vehicle
     *
     * @Route("/vehicle/action/{vehicle_id}/{limit}",
     *    name="vehicle_action_list",
     *    defaults={"limit": "100"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleAction::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="vehicle_id",
     *     in="path",
     *     description="vehicle  can be null",
     *     type="integer",
     *     default="null"
     * )
     * @SWG\Tag(name="VehicleAction")
     */
    public function listByVehicle(Request $request, $vehicle_id = null, $limit = 100)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $action = $this->vehicleService->listActionByVehicle($vehicle_id, $limit);

        return new JsonResponse($action);
    }


//LIST BETWEEN DATE
    /**
     * Lists all the vehicle action between date
     *
     * @Route("/vehicle/action/between/{from}/{to}/{vehicle_id}/{limit}",
     *    name="vehicle_action_between_date",
     *    defaults={"vehicle_id": null, "limit" : null},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleAction::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="date action can be null",
     *     type="string",
     *     default="null"
     * )
     * @SWG\Parameter(
     *     name="vehicle_id",
     *     in="path",
     *     description="vehicle  can be null",
     *     type="integer",
     *     default="null"
     * )
     * @SWG\Tag(name="VehicleAction")
     */
    public function listBetweenDate(Request $request, $from, $to, $vehicle_id = null, $limit = null)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $action = $this->vehicleService->listActionBetweenDate($from, $to, $vehicle_id, $limit);

        return new JsonResponse($action);
    }

//LIST
    /**
     * Lists all the vehicle action by date
     *
     * @Route("/vehicle/action/{date_action}/{vehicle_id}",
     *    name="vehicle_action_list_date",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleAction::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="date action can be null",
     *     type="string",
     *     default="null"
     * )
     * @SWG\Parameter(
     *     name="vehicle_id",
     *     in="path",
     *     description="vehicle  can be null",
     *     type="integer",
     *     default="null"
     * )
     * @SWG\Tag(name="VehicleAction")
     */
    public function listByDate(Request $request, $date_action = null, $vehicle_id = null)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $action = $this->vehicleService->listActionByDate($date_action, $vehicle_id);

        return new JsonResponse($action);
    }

}
