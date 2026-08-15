<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\VehicleFuel;
use App\Form\VehicleType;
use App\Entity\VehicleItem;
use App\Entity\VehicleCheckup;
use App\Service\VehicleItemServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * VehicleItemController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleItemController extends AbstractController
{
    private $vehicleItemService;

    public function __construct(VehicleItemServiceInterface $vehicleItemService)
    {
        $this->vehicleItemService = $vehicleItemService;
    }

//LIST
    /**
     * Lists all items the vehicle
     *
     * @Route("/vehicle/item/list",
     *    name="vehicle_item_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleItem::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="VehicleItem")
     */
    public function list(Request $request)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $items = $this->vehicleItemService->list();

        return new JsonResponse($items);
    }

//CREATE CHECKUP
    /**
     * valid checkup
     *
     * @Route("/vehicle/checkup/valid",
     *    name="vehicle_checkup_valid",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleCheckup::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="VehicleCheckup")
     */
    public function validCheckup(Request $request)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $checkup = $this->vehicleItemService->validCheckup($request->getContent());

        return new JsonResponse($checkup);
    }


//LIST ALL C_UP
    /**
     * Lists all checkup by vehicle
     *
     * @Route("/vehicle/checkup/list/{vehicle_id}",
     *    name="vehicle_checkup_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleCheckup::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="VehicleCheckup")
     */
    public function checkupVehicleList(Request $request, $vehicle_id)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $items = $this->vehicleItemService->checkupVehicleList($vehicle_id);
        return new JsonResponse($items);
    }



//VEHICULE NEED CHECKUP
    /**
     * Lists all vehicule need checlup
     *
     * @Route("/vehicle/need/checkup",
     *    name="vehicle_need_checkup",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=VehicleCheckup::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="VehicleCheckup")
     */
    public function vehicleNeedCheckup()
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $items = $this->vehicleItemService->vehicleNeedCheckup();

        return new JsonResponse($items);
    }

}
