<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Service\LocationServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * LocationController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class LocationController extends AbstractController
{
    private $locationService;

    public function __construct(LocationServiceInterface $locationService)
    {
        $this->locationService = $locationService;
    }

//LIST
    /**
     * Lists all the locations
     *
     * @Route("/location/list/{group_front}",
     *    name="location_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Location::class))
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
     * @SWG\Tag(name="Location")
     */
    public function listAll(Request $request, PaginatorInterface $paginator, $group_front = null)
    {

        $locations = $this->locationService->findAll($group_front)->getResult();

        $locationsArray = array();
        foreach ($locations as $location) {
            $locationsArray[] = $this->locationService->toArray($location);
        };

        return new JsonResponse($locationsArray);
    }

//DISPLAY
    /**
     * Displays location
     *
     * @Route("/location/display/{locationId}",
     *    name="location_display",
     *    requirements={"locationId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("location", expr="repository.findOneById(locationId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Location::class),
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
     *     name="locationId",
     *     in="path",
     *     required=true,
     *     description="Id of the location",
     *     type="integer",
     * )
     * @SWG\Tag(name="Location")
     */
    public function display(Request $request, Location $location)
    {
        $this->denyAccessUnlessGranted('locationDisplay', $location);

        $locationArray = $this->locationService->toArray($location);

        return new JsonResponse($locationArray);
    }



    /**
     * Update Visibilibt
     *
     * @Route("/location/update/visibility/{locationId}/{front_visibility}",
     *    name="location_update_visibility",
     *    requirements={"locationId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("location", expr="repository.findOneById(locationId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Location::class),
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
     *     name="locationId",
     *     in="path",
     *     required=true,
     *     description="Id of the location",
     *     type="integer",
     * )
     * @SWG\Tag(name="Location")
     */
    public function updateFrontVisibility(Location $location, $front_visibility) {
        $this->denyAccessUnlessGranted('locationDisplay', $location);

        $locationArray = $this->locationService->updateVisibility($location, $front_visibility);

        return new JsonResponse($locationArray);
    }

//CREATE
    /**
     * Creates a location
     *
     * @Route("/location/create",
     *    name="location_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="location", ref=@Model(type=Location::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Location",
     *     required=true,
     *     @Model(type=LocationType::class)
     * )
     * @SWG\Tag(name="Location")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('locationCreate');

        $createdData = $this->locationService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies location
     *
     * @Route("/location/modify/{locationId}",
     *    name="location_modify",
     *    requirements={"locationId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("location", expr="repository.findOneById(locationId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="location", ref=@Model(type=Location::class)),
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
     *     name="locationId",
     *     in="path",
     *     required=true,
     *     description="Id of the location",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Location",
     *     required=true,
     *     @Model(type=LocationType::class)
     * )
     * @SWG\Tag(name="Location")
     */
    public function modify(Request $request, Location $location)
    {
        $this->denyAccessUnlessGranted('locationModify', $location);

        $modifiedData = $this->locationService->modify($location, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes location
     *
     * @Route("/location/delete/{locationId}",
     *    name="location_delete",
     *    requirements={"locationId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("location", expr="repository.findOneById(locationId)")
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
     *     name="locationId",
     *     in="path",
     *     required=true,
     *     description="Id of the location",
     *     type="integer",
     * )
     * @SWG\Tag(name="Location")
     */
    public function delete(Location $location)
    {
        $this->denyAccessUnlessGranted('locationDelete', $location);

        $suppressedData = $this->locationService->delete($location);

        return new JsonResponse($suppressedData);
    }
}
