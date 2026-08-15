<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Service\ReminderServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ReminderController class
 * @author Sandy Razafitrimo
 */
class ReminderController extends AbstractController
{
    private $notificationService;

    public function __construct(ReminderServiceInterface $reminderService)
    {
        $this->reminderService = $reminderService;
    }


//CREATE
    /**
     * Creates a reminder
     *
     * @Route("/reminder/create",
     *    name="reminder_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="reminder", ref=@Model(type=reminder::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the reminder",
     *     required=true,
     *     @Model(type=ReminderType::class)
     * )
     * @SWG\Tag(name="reminder")
     */
    public function create(Request $request)
    {
        //$this->denyAccessUnlessGranted('mealCreate');

        $createdData = $this->reminderService->create($request->getContent());

        return new JsonResponse($createdData);
    }

    // Update
    /**
     * Creates a reminder
     *
     * @Route("/reminder/update/{reminderId}",
     *    name="reminder_update",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="reminder", ref=@Model(type=reminder::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the reminder",
     *     required=true,
     *     @Model(type=ReminderType::class)
     * )
     * @SWG\Tag(name="reminder")
     */
    public function update($reminderId, Request $request)
    {
        //$this->denyAccessUnlessGranted('mealCreate');

        $createdData = $this->reminderService->modify($reminderId, $request->getContent());

        return new JsonResponse($createdData);
    }

    //RETRIEVE
    /**
     * Retrieve a reminder
     *
     * @Route("/reminder/list/{status}/{vehicle_id}",
     *    name="reminder_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="string"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="reminder", ref=@Model(type=reminder::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the reminder",
     *     required=true,
     *     @Model(type=ReminderType::class)
     * )
     * @SWG\Tag(name="reminder")
     */
    public function list($status = "all", $vehicle_id = null)
    {
        //$this->denyAccessUnlessGranted('mealCreate');

        $createdData = $this->reminderService->list($status, $vehicle_id);

        return new JsonResponse($createdData);
    }


    //UPDATE STATUS
    /**
     * Update a reminder
     *
     * @Route("/reminder/nextStatus/{reminderId}",
     *    name="reminder_next_status",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="string"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="reminder", ref=@Model(type=reminder::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="reminder")
     */
    public function nextStatus($reminderId)
    {
        //$this->denyAccessUnlessGranted('mealCreate');

        $createdData = $this->reminderService->nextStatus($reminderId);

        return new JsonResponse($createdData);
    }

}
