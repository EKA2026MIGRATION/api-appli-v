<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Form\PhoneType;
use App\Service\PhoneServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * PhoneController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PhoneController extends AbstractController
{
    private $phoneService;

    public function __construct(PhoneServiceInterface $phoneService)
    {
        $this->phoneService = $phoneService;
    }

//DISPLAY
    /**
     * Displays phone
     *
     * @Route("/phone/display/{phoneId}",
     *    name="phone_display",
     *    requirements={"phoneId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("phone", expr="repository.findOneById(phoneId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Phone::class)
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
     *     name="phoneId",
     *     in="path",
     *     description="Id of the phone",
     *     type="integer",
     * )
     * @SWG\Tag(name="Phone")
     */
    public function display(Phone $phone)
    {
        $this->denyAccessUnlessGranted('phoneDisplay', $phone);

        $phoneArray = $this->phoneService->toArray($phone);

        return new JsonResponse($phoneArray);
    }



//UPDATE IS PREFERED
    /**
     * Change phone is prefered
     *
     * @Route("/phone/isPrefered/{phoneId}/{type}",
     *    name="phone_isPrefered",
     *    requirements={"phoneId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("phone", expr="repository.findOneById(phoneId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Phone::class)
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
     *     name="phoneId",
     *     in="path",
     *     description="Id of the phone",
     *     type="integer",
     * )
     * @SWG\Tag(name="Phone")
     */
    public function isPrefered(Phone $phone, $type)
    {
        $this->denyAccessUnlessGranted('phoneDisplay', $phone);
        $result = $this->phoneService->updateIsPrefered($phone, $type);
        return new JsonResponse($result);
    }



//CREATE
    /**
     * Creates phone
     *
     * @Route("/phone/create",
     *    name="phone_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="phone", ref=@Model(type=Phone::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Phone",
     *     required=true,
     *     @Model(type=PhoneType::class)
     * )
     * @SWG\Tag(name="Phone")
     */
    public function create(Request $request)
    {
      //  $this->denyAccessUnlessGranted('phoneCreate');

        $createdData = $this->phoneService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies phone
     *
     * @Route("/phone/modify/{phoneId}",
     *    name="phone_modify",
     *    requirements={"phoneId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("phone", expr="repository.findOneById(phoneId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="phone", ref=@Model(type=Phone::class)),
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
     *     name="phoneId",
     *     in="path",
     *     description="Id for the phone",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Phone",
     *     required=true,
     *     @Model(type=PhoneType::class)
     * )
     * @SWG\Tag(name="Phone")
     */
    public function modify(Request $request, Phone $phone)
    {
        $this->denyAccessUnlessGranted('phoneModify', $phone);

        $modifiedData = $this->phoneService->modify($phone, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes phone
     *
     * @Route("/phone/delete/{phoneId}",
     *    name="phone_delete",
     *    requirements={"phoneId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("phone", expr="repository.findOneById(phoneId)")
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
     *     name="phoneId",
     *     in="path",
     *     description="Id for the phone",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Phone")
     */
    public function delete(Phone $phone)
    {
        $this->denyAccessUnlessGranted('phoneDelete', $phone);

        $suppressedData = $this->phoneService->delete($phone);

        return new JsonResponse($suppressedData);
    }
}
