<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\VehicleWashing;
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
 * VehicleFuelController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleWashingController extends AbstractController
{
    private $vehicleService;

    public function __construct(VehicleServiceInterface $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

//ADD
    /**
     * Add washing to a Vehicle
     *
     * @Route("/vehicle/add/washing",
     *    name="vehicle_add_washing",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="vehicle", ref=@Model(type=VehicleWashing::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="VehicleWashing")
     */
    public function addWashing(Request $request)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $createdData = $this->vehicleService->addWashing($request->getContent());

        return new JsonResponse($createdData);
    }


//LIST BY VEHICLE
    /**
     * Lists all the washing by vehicle
     *
     * @Route("/vehicle/washing/{vehicle_id}/{limit}",
     *    name="vehicle_washing_list",
     *    defaults={"limit": "100"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleWashing::class))
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
     * @SWG\Tag(name="VehicleWashing")
     */
    public function listByVehicle(Request $request, $vehicle_id = null, $limit = 100)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $action = $this->vehicleService->listWashingByVehicle($vehicle_id, $limit);

        return new JsonResponse($action);
    }


//LIST BETWEEN DATE
    /**
     * Lists all the vehicle fuel between date
     *
     * @Route("/vehicle/washing/between/{from}/{to}/{vehicle_id}/{limit}",
     *    name="vehicle_washing_between_date",
     *    defaults={"vehicle_id": null, "limit" : null},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleWashing::class))
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
     * @SWG\Tag(name="VehicleWashing")
     */
    public function listBetweenDate(Request $request, $from, $to, $vehicle_id = null, $limit = null)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $action = $this->vehicleService->listWashingBetweenDate($from, $to, $vehicle_id, $limit);

        return new JsonResponse($action);
    }

//LIST
    /**
     * Lists all the vehicle washing by date
     *
     * @Route("/vehicle/washing/{date_action}/{vehicle_id}",
     *    name="vehicle_washing_list_date",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleWashing::class))
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
     * @SWG\Tag(name="VehicleWashing")
     */
    public function listByDate(Request $request, $date_action = null, $vehicle_id = null)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $action = $this->vehicleService->listWashingByDate($date_action, $vehicle_id);

        return new JsonResponse($action);
    }

}
