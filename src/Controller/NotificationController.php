<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Service\NotificationServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * NotificationController class
 * @author Sandy Razafitrimo
 */
class NotificationController extends AbstractController
{
    private $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }


//CREATE
    /**
     * Creates a Notification
     *
     * @Route("/notification/create",
     *    name="notification_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="notification", ref=@Model(type=notification::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the notification",
     *     required=true,
     *     @Model(type=NotificationType::class)
     * )
     * @SWG\Tag(name="notification")
     */
    public function create(Request $request)
    {
        //$this->denyAccessUnlessGranted('mealCreate');

        $createdData = $this->notificationService->create($request->getContent());

        return new JsonResponse($createdData);
    }

    //RETRIEVE
    /**
     * Creates a Notification
     *
     * @Route("/notification/byPersonId/{person_id}",
     *    name="notification_by_person_id",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="notification", ref=@Model(type=notification::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the notification",
     *     required=true,
     *     @Model(type=NotificationType::class)
     * )
     * @SWG\Tag(name="notification")
     */
    public function findByPersonId($person_id)
    {
        //$this->denyAccessUnlessGranted('mealCreate');

        $createdData = $this->notificationService->findByPersonId($person_id);

        return new JsonResponse($createdData);
    }

//RETRIEVE
    /**
     * Creates a Notification
     *
     * @Route("/notification/removePerson/{notificationId}/{personId}",
     *    name="notification_remover_person",
     *    methods={"HEAD", "DELETE"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="notification", ref=@Model(type=notification::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the notification",
     *     required=true,
     *     @Model(type=NotificationType::class)
     * )
     * @SWG\Tag(name="notification")
     */
    public function removePerson($notificationId, $personId)
    {
        //$this->denyAccessUnlessGranted('mealCreate');

        $createdData = $this->notificationService->removePerson($notificationId, $personId);

        return new JsonResponse($createdData);
    }

    //RETRIEVE
    /**
     * Creates a Notification
     *
     * @Route("/notification/deleteAllByPersonId/{personId}",
     *    name="delete_by_person_id",
     *    methods={"HEAD", "DELETE"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="notification", ref=@Model(type=notification::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the notification",
     *     required=true,
     *     @Model(type=NotificationType::class)
     * )
     * @SWG\Tag(name="notification")
     */
    public function deleteByPersonId($personId)
    {
        //$this->denyAccessUnlessGranted('mealCreate');

        $createdData = $this->notificationService->deleteAllByPersonId($personId);

        return new JsonResponse($createdData);
    }
}
