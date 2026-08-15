<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\VehicleCheckup;
use App\Form\VehicleType;
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
 * VehicleController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class VehicleController extends AbstractController
{
    private $vehicleService;

    public function __construct(VehicleServiceInterface $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

//LIST
    /**
     * Lists all the vehicles
     *
     * @Route("/vehicle/list",
     *    name="vehicle_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Vehicle::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
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
     * @SWG\Tag(name="Vehicle")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('vehicleList');

        $vehicles = $paginator->paginate(
            $this->vehicleService->findAllInArray(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        return new JsonResponse($vehicles->getItems());
    }

    
  //LIST
      /**
       * Lists all the vehicle information between
       *
       * @Route("/vehicle/data/{type}/{from}/{to}/{limit}/{vehicle_id}",
       *    name="vehicle_list_data",
       *    defaults={"vehicle_id": null, "limit" : null},
       *    methods={"HEAD", "GET"})
       *
       * @SWG\Response(
       *     response=200,
       *     description="Success",
       *     @SWG\Schema(
       *         type="array",
       *         @SWG\Items(ref=@Model(type=Vehicle::class))
       *     )
       * )
       * @SWG\Response(
       *     response=403,
       *     description="Access denied",
       * )
       * @SWG\Tag(name="Vehicle")
       */
       public function listBetweenDate(Request $request, $type,  $from, $to, $limit = null, $vehicle_id = null)
      {
          $this->denyAccessUnlessGranted('vehicleList');

          $action = $this->vehicleService->listVehicleBetweenDate($type, $from, $to, $limit, $vehicle_id);

          return new JsonResponse($action);
      }


//DISPLAY
    /**
     * Displays the vehicle using its id
     *
     * @Route("/vehicle/display/{vehicleId}",
     *    name="vehicle_list_id",
     *    requirements={"date": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("vehicle", expr="repository.findOneById(vehicleId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Vehicle::class))
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
     *     name="vehicleId",
     *     in="path",
     *     description="Id for the vehicle",
     *     type="string",
     * )
     * @SWG\Tag(name="Vehicle")
     */
    public function display(Vehicle $vehicle)
    {
        $this->denyAccessUnlessGranted('vehicleDisplay', $vehicle);

        $vehicleArray = $this->vehicleService->toArray($vehicle);

        return new JsonResponse($vehicleArray);
    }

//CREATE
    /**
     * Creates a Vehicle
     *
     * @Route("/vehicle/create",
     *    name="vehicle_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="vehicle", ref=@Model(type=Vehicle::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Vehicle",
     *     required=true,
     *     @Model(type=VehicleType::class)
     * )
     * @SWG\Tag(name="Vehicle")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('vehicleCreate');

        $createdData = $this->vehicleService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies vehicle
     *
     * @Route("/vehicle/modify/{vehicleId}",
     *    name="vehicle_modify",
     *    requirements={"vehicleId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("vehicle", expr="repository.findOneById(vehicleId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="vehicle", ref=@Model(type=Vehicle::class)),
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
     *     name="vehicleId",
     *     in="path",
     *     required=true,
     *     description="Id of the vehicle",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Vehicle",
     *     required=true,
     *     @Model(type=VehicleType::class)
     * )
     * @SWG\Tag(name="Vehicle")
     */
    public function modify(Request $request, Vehicle $vehicle)
    {
        $this->denyAccessUnlessGranted('vehicleModify', $vehicle);

        $modifiedData = $this->vehicleService->modify($vehicle, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes vehicle and moves all the pickups as "Non pris en charge"
     *
     * @Route("/vehicle/delete/{vehicleId}",
     *    name="vehicle_delete",
     *    requirements={"vehicleId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("vehicle", expr="repository.findOneById(vehicleId)")
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
     *     name="vehicleId",
     *     in="path",
     *     required=true,
     *     description="Id of the vehicle",
     *     type="integer",
     * )
     * @SWG\Tag(name="Vehicle")
     */
    public function delete(Vehicle $vehicle)
    {
        $this->denyAccessUnlessGranted('vehicleDelete', $vehicle);

        $suppressedData = $this->vehicleService->delete($vehicle);

        return new JsonResponse($suppressedData);
    }



//DELETE ELEMENT
    /**
     * Deletes on element on vehicle
     *
     * @Route("/vehicle/remove/{type}/{elementId}",
     *    name="vehicle_delete_element",
     *    methods={"HEAD", "DELETE"})   
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
     * @SWG\Tag(name="Vehicle")
     */
    public function deleteElement($type, $elementId)
    {

        $suppressedData = $this->vehicleService->removeElement($type, $elementId);

        return new JsonResponse($suppressedData);
    }
}
