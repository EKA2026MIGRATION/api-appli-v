<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Service\AddressServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * AddressController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class AddressController extends AbstractController
{
    private $addressService;

    public function __construct(AddressServiceInterface $addressService)
    {
        $this->addressService = $addressService;
    }

//DISPLAY
    /**
     * Displays address
     *
     * @Route("/address/display/{addressId}",
     *    name="address_display",
     *    requirements={"addressId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("address", expr="repository.findOneById(addressId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Address::class)
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
     *     name="addressId",
     *     in="path",
     *     description="Id of the address",
     *     type="integer",
     * )
     * @SWG\Tag(name="Address")
     */
    public function display(Address $address)
    {
        $this->denyAccessUnlessGranted('addressDisplay', $address);

        $addressArray = $this->addressService->toArray($address);

        return new JsonResponse($addressArray);
    }

//CREATE
    /**
     * Creates address
     *
     * @Route("/address/create",
     *    name="address_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="address", ref=@Model(type=Address::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Address",
     *     required=true,
     *     @Model(type=AddressType::class)
     * )
     * @SWG\Tag(name="Address")
     */
    public function create(Request $request)
    {
      //  $this->denyAccessUnlessGranted('addressCreate');

        $createdData = $this->addressService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies address
     *
     * @Route("/address/modify/{addressId}",
     *    name="address_modify",
     *    requirements={"addressId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("address", expr="repository.findOneById(addressId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="address", ref=@Model(type=Address::class)),
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
     *     name="addressId",
     *     in="path",
     *     description="Id for the address",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Address",
     *     required=true,
     *     @Model(type=AddressType::class)
     * )
     * @SWG\Tag(name="Address")
     */
    public function modify(Request $request, Address $address)
    {
        $this->denyAccessUnlessGranted('addressModify', $address);

        $modifiedData = $this->addressService->modify($address, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes address
     *
     * @Route("/address/delete/{addressId}",
     *    name="address_delete",
     *    requirements={"addressId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("address", expr="repository.findOneById(addressId)")
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
     *     name="addressId",
     *     in="path",
     *     description="Id for the address",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Address")
     */
    public function delete(Address $address)
    {
        $this->denyAccessUnlessGranted('addressDelete', $address);

        $suppressedData = $this->addressService->delete($address);

        return new JsonResponse($suppressedData);
    }

//GEOCODING
    /**
     * Geocodes all the Address
     *
     * @Route("/address/geocode",
     *    name="address_geocode",
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
     * @SWG\Tag(name="Address")
     */
    public function geocode()
    {
        set_time_limit(600);

        $this->denyAccessUnlessGranted('addressGeocode');

        $counterRecords = $this->addressService->geocode();

        return new JsonResponse(array('Number of records treated' => $counterRecords));
    }
}
