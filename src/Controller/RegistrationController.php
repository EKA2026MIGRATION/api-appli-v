<?php

namespace App\Controller;

use App\Entity\Registration;
use App\Form\RegistrationType;
use App\Service\RegistrationServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * RegistrationController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RegistrationController extends AbstractController
{
    private $registrationService;

    public function __construct(RegistrationServiceInterface $registrationService)
    {
        $this->registrationService = $registrationService;
    }

//LIST BY STATUS
    /**
     * Lists all the registrations
     *
     * @Route("/registration/list/{status}",
     *    name="registration_list",
     *    requirements={"status": "^(cart|in-progress|payed)$"},
     *    defaults={"status": "null"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Registration::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="null|cart|in-progress|payed registrations",
     *     type="string",
     *     default="null",
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
     * @SWG\Tag(name="Registration")
     */
    public function listAll(Request $request, $status, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('registrationList');

        $registrations = $paginator->paginate(
            $this->registrationService->findAllByStatus($status),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 100)
        );

        $registrationsArray = array();
        foreach ($registrations->getItems() as $registration) {
            $registrationsArray[] = $this->registrationService->toArray($registration);
        };

        return new JsonResponse($registrationsArray);
    }

    //LIST BY CHILD ID AND DATE FROM TO
    /**
     * Lists all the registrations ny child id
     *
     * @Route("/registration/childList/{childId}/{from}/{to}",
     *    name="registration_list_by_child",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Registration::class))
     *     )
     * )
     * @SWG\Tag(name="Registration")
     */
    public function listByChild(Request $request, $childId, $from, $to)
    {
        $registrationsArray = $this->registrationService->findAllByChild($childId, $from, $to);        
        return new JsonResponse($registrationsArray);
    }


//LIST ALL AWAITING PAYMENT
    /**
     * Lists all the registrations excepting those with cart status
     *
     * @Route("/registration/awaiting-payment",
     *    name="registration_awaiting_payment",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Registration::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Registration")
     */
    public function awaitingPayment(Request $request)
    {

        $registrationsArray = $this->registrationService->awaitingPayment();
        return new JsonResponse($registrationsArray);
    }




//LIST BY ALL EXCEPT CART STATUS
    /**
     * Lists all the registrations excepting those with cart status
     *
     * @Route("/registration/list/without-cart",
     *    name="registration_list_without_cart",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Registration::class))
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
     * @SWG\Tag(name="Registration")
     */
    public function listAllWithoutCart(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('registrationListWithoutCart');

        $registrations = $paginator->paginate(
            $this->registrationService->findAllWithoutCart(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 100)
        );

        $registrationsArray = array();
        foreach ($registrations->getItems() as $registration) {
            $registrationsArray[] = $this->registrationService->toArray($registration);
        };

        return new JsonResponse($registrationsArray);
    }

//LIST BY PERSONID AND STATUS
    /**
     * Lists all the registrations
     *
     * @Route("/registration/list/{personId}/{status}",
     *    name="registration_list_person_status",
     *    requirements={
     *        "personId": "^([0-9]+)$",
     *        "status": "^(cart|in-progress|payed|waiting)$"
     *    },
     *    defaults={"status": "null"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Registration::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="personId",
     *     in="path",
     *     required=true,
     *     description="Id of the person",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="null|cart|in-progress|payed|waiting registrations",
     *     type="string",
     *     default="null",
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
     * @SWG\Tag(name="Registration")
     */
    public function listByPersonStatus(Request $request, int $personId, $status, PaginatorInterface $paginator)
    {
        //$this->denyAccessUnlessGranted('registrationList', $personId);
        $registrationsArray = $this->registrationService->findAllByPersonAndStatus($personId, $status);
        return new JsonResponse($registrationsArray);
    }

//LIST BY childId AND STATUS
    /**
     * Lists all the registrations
     *
     * @Route("/registration/child-list/{childId}/{status}",
     *    name="registration_list_child_status",
     *    requirements={
     *        "personId": "^([0-9]+)$",
     *        "status": "^(cart|in-progress|payed|waiting)$"
     *    },
     *    defaults={"status": "null"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Registration::class))
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
     *     name="status",
     *     in="path",
     *     description="null|cart|in-progress|payed|waiting registrations",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Tag(name="Registration")
     */
    public function listByChildStatus(Request $request, int $childId, $status)
    {
       // $this->denyAccessUnlessGranted('registrationList', $personId);

        $registrationsArray = $this->registrationService->findAllByChildStatus($childId, $status);
        return new JsonResponse($registrationsArray);
    }


//DISPLAY
    /**
     * Displays registration
     *
     * @Route("/registration/display/{registrationId}",
     *    name="registration_display",
     *    requirements={"registrationId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("registration", expr="repository.findOneById(registrationId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Registration::class),
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
     *     description="Id of the registration",
     *     type="integer",
     * )
     * @SWG\Tag(name="Registration")
     */
    public function display(Request $request, Registration $registration)
    {
        $this->denyAccessUnlessGranted('registrationDisplay', $registration);

        $registrationArray = $this->registrationService->toArray($registration);

        return new JsonResponse($registrationArray);
    }

//CREATE
    /**
     * Creates a registration form client view (inscription en ligne)
     *
     * @Route("/registration/create",
     *    name="registration_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="registration", ref=@Model(type=Registration::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Registration",
     *     required=true,
     *     @Model(type=RegistrationType::class)
     * )
     * @SWG\Tag(name="Registration")
     */
    public function create(Request $request)
    {
      //  $this->denyAccessUnlessGranted('registrationCreate'); // changer la route pour le client

        $createdData = $this->registrationService->create($request->getContent());

        return new JsonResponse($createdData);
    }


//CREATE
    /**
     * Creates a registration form client view (inscription en ligne)
     *
     * @Route("/registration/updateStatus",
     *    name="registration_updateStatus",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="registration", ref=@Model(type=Registration::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Registration",
     *     required=true,
     *     @Model(type=RegistrationType::class)
     * )
     * @SWG\Tag(name="Registration")
     */
    public function updateStatus(Request $request)
    {
        $createdData = $this->registrationService->updateStatus($request->getContent());

        return new JsonResponse($createdData);
    }


    /**
     * List all stats analyse strat by name
     * @Route("/registration/bulkUpdateStatus", name="bulk_update_status", methods={"HEAD", "POST"})
     * @param $name
     * @return JsonResponse
     */
    public function bulkUpdateStatus(Request $request)
    {
        $createdData = $this->registrationService->bulkUpdateStatus($request->getContent());

        return new JsonResponse($createdData);
    }




//MODIFY
    /**
     * Modifies registration
     *
     * @Route("/registration/modify/{registrationId}",
     *    name="registration_modify",
     *    requirements={"registrationId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("registration", expr="repository.findOneById(registrationId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="registration", ref=@Model(type=Registration::class)),
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
     *     description="Id of the registration",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Registration",
     *     required=true,
     *     @Model(type=RegistrationType::class)
     * )
     * @SWG\Tag(name="Registration")
     */
    public function modify(Request $request, Registration $registration)
    {
      //  $this->denyAccessUnlessGranted('registrationModify', $registration); // changer la route pour le client

        $modifiedData = $this->registrationService->modify($registration, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes registration
     *
     * @Route("/registration/delete/{registrationId}",
     *    name="registration_delete",
     *    requirements={"registrationId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("registration", expr="repository.findOneById(registrationId)")
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
     *     description="Id of the registration",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Registration",
     *     required=true,
     *     @Model(type=RegistrationType::class)
     * )
     * @SWG\Tag(name="Registration")
     */
    public function delete(Registration $registration)
    {
      //  $this->denyAccessUnlessGranted('registrationDelete', $registration); // changer la route pour le client

        $suppressedData = $this->registrationService->delete($registration);

        return new JsonResponse($suppressedData);
    }



}
