<?php

namespace App\Controller;

use App\Entity\StaffPresence;
use App\Form\StaffPresenceType;
use App\Service\StaffPresenceServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * StaffPresenceController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class StaffPresenceController extends AbstractController
{
    private $staffPresenceService;

    public function __construct(StaffPresenceServiceInterface $staffPresenceService)
    {
        $this->staffPresenceService = $staffPresenceService;
    }

//LIST
    /**
     * Lists all the staff presences by kind of staff and date
     *
     * @Route("/staff/presence/list/{kind}/{date}/{status}",
     *    name="staff_presence_list_date",
     *    requirements={
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
     *        "kind": "^([a-zA-Z]+)$"
     *    },
     *    defaults={"kind": "all"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StaffPresence::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="kind",
     *     in="path",
     *     description="Kind for the staff (Default: all)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the staff presence (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *      name="statis",
     *      in="path",
     *      description="Status of presence",
     *      type="string",
     *  )
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
     * @SWG\Tag(name="StaffPresence")
     */
    public function listAll(Request $request, $kind, $date, PaginatorInterface $paginator, $status = null)
    {
        $this->denyAccessUnlessGranted('staffPresenceList');

        $staffPresencesArray = array();
        $staffPresences = $this->staffPresenceService->findAllByKindAndDate($kind, $date, $status);
        foreach ($staffPresences as $staffPresence) {
            $staffPresencesArray[] = $this->staffPresenceService->toArray($staffPresence);
        };

        return new JsonResponse($staffPresencesArray);
    }

//DISPLAY
    /**
     * Displays staffPresence using staffId and date (optional)
     *
     * @Route("/staff/presence/display/{staffId}/{date}",
     *    name="staff_presence_display",
     *    requirements={
     *        "staffId": "^([0-9]+)",
     *        "date": "^(all|([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$"
     *    },
     *    defaults={"date": "all"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StaffPresence::class))
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
     *     name="staffId",
     *     in="path",
     *     required=true,
     *     description="Id of the staff",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the staff presence (all | YYYY-MM-DD | YYYY-MM | YYYY (default: all))",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Tag(name="StaffPresence")
     */
    public function display($staffId, $date)
    {
        $this->denyAccessUnlessGranted('staffPresenceDisplay');

        $staffPresencesArray = array();
        foreach ($this->staffPresenceService->findByStaff($staffId, $date) as $staffPresence) {
            $staffPresencesArray[] = $this->staffPresenceService->toArray($staffPresence);
        };

        return new JsonResponse($staffPresencesArray);
    }


//DISPLAY WORKLOAD
    /**
     * Displays workload by staff between 2 dates and staffId(optionnel)
     *
     * @Route("/staff/presence/workload/{date_from}/{date_to}/{staffId}",
     *    name="staff_presence_workload",
     *    requirements={
     *        "staffId": "^([0-9]+)",
     *        "date_from": "^(all|([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$",
     *        "date_to": "^(all|([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$"

     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StaffPresence::class))
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
     *     name="staffId",
     *     in="path",
     *     default="null",
     *     description="Id of the staff",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="date_from",
     *     in="path",
     *     description="Date for the staff presence (YYYY-MM-DD)",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Parameter(
     *     name="date_to",
     *     in="path",
     *     description="Date for the staff presence (YYYY-MM-DD)",
     *     type="string",
     *     default="null"
     * )
     * @SWG\Tag(name="StaffPresence")
     */
    public function staffWorkloads($date_from, $date_to, $staffId = null)
    {
        //$this->denyAccessUnlessGranted('staffPresenceDisplay');
        $staffWorkloads = $this->staffPresenceService->getWorkloads($date_from, $date_to, $staffId);
        return new JsonResponse($staffWorkloads);
    }




    /**
     * Displays prsence optimize code between 2 dates and staffId(optionnel)
     *
     * @Route("/staff/presence/speed/{date_from}/{date_to}/{staffId}",
     *    name="staff_presence_speed",
     *    requirements={
     *        "staffId": "^([0-9]+)",
     *        "date_from": "^(all|([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$",
     *        "date_to": "^(all|([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$"

     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StaffPresence::class))
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
     *     name="staffId",
     *     in="path",
     *     default="null",
     *     description="Id of the staff",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="date_from",
     *     in="path",
     *     description="Date for the staff presence (YYYY-MM-DD)",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Parameter(
     *     name="date_to",
     *     in="path",
     *     description="Date for the staff presence (YYYY-MM-DD)",
     *     type="string",
     *     default="null"
     * )
     * @SWG\Tag(name="StaffPresence")
     */
    public function staffPresenceSpeed($date_from, $date_to, $staffId = null)
    {
        $presence = $this->staffPresenceService->getPresenceSpeed($date_from, $date_to, $staffId);
        return new JsonResponse($presence);
    }







//DISPLAY TOTALS BY SEASON
    /**
     * Displays the totals of staffPresence by season
     *
     * @Route("/staff/presence/total/{seasonId}",
     *    name="staff_presence__total",
     *    requirements={"seasonId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StaffPresence::class))
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
     *     name="seasonId",
     *     in="path",
     *     required=true,
     *     description="Id of the season",
     *     type="integer",
     * )
     * @SWG\Tag(name="StaffPresence")
     */
    public function total(int $seasonId)
    {
        $this->denyAccessUnlessGranted('staffPresenceDisplay');

        $totals = $this->staffPresenceService->getTotals($seasonId);

        return new JsonResponse($totals);
    }


//DISPLAY ALLS BY SEASON AND STAFF
    /**
     * Displays the alls of staffPresence by season & STAFF
     *
     * @Route("/staff/presence/{seasonId}/{staffId}",
     *    name="staff_presence_total_by_season",
     *    requirements={"seasonId": "^([0-9]+)$"},
     *    requirements={"staffId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StaffPresence::class))
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
     *     name="seasonId",
     *     in="path",
     *     required=true,
     *     description="Id of the season",
     *     type="integer",
     * )
     * @SWG\Tag(name="StaffPresence")
     */
    public function allPresences(int $seasonId, int $staffId)
    {
        $this->denyAccessUnlessGranted('staffPresenceDisplay');

        $staffPresences = $this->staffPresenceService->getPresenceBySeasonAndStaff($seasonId, $staffId);

        return new JsonResponse($staffPresences);
    }

//CREATE
    /**
     * Creates StaffPresence
     *
     * @Route("/staff/presence/create",
     *    name="staff_presence_create",
     *    methods={"HEAD", "POST"})
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
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the StaffPresence",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StaffPresenceType::class))
     *     )
     * )
     * @SWG\Tag(name="StaffPresence")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('staffPresenceCreate');

        $createdData = $this->staffPresenceService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//CREATE
    /**
     * UPDATE TYPE OF STAFFPRESENCE
     *
     * @Route("/staff/presence/modify/{staffPresenceId}/{typeName}",
     *    name="staff_presence_modify_typeName",
     *    methods={"HEAD", "GET"})
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
     * @SWG\Tag(name="StaffPresence")
     */
    public function modifyTypeName(Request $request, $staffPresenceId, $typeName)
    {
        //$this->denyAccessUnlessGranted('staffPresenceCreate');

        $createdData = $this->staffPresenceService->modifyTypeName($staffPresenceId, $typeName);

        return new JsonResponse($createdData);
    }


//DELETE BY ID
    /**
     * Deletes staffPresence using its id
     *
     * @Route("/staff/presence/delete/{staffPresenceId}",
     *    name="staff_presence_delete",
     *    requirements={"staffPresenceId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("component", expr="repository.findOneById(staffPresenceId)")
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
     *     name="staffPresenceId",
     *     in="path",
     *     description="Id for the StaffPresence",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="StaffPresence")
     */
    public function delete(StaffPresence $staffPresence)
    {
        $this->denyAccessUnlessGranted('staffPresenceDelete', $staffPresence);

        $suppressedData = $this->staffPresenceService->delete($staffPresence);

        return new JsonResponse($suppressedData);
    }

//DELETE BY ARRAY OF IDS
    /**
     * Deletes staffPresence
     *
     * @Route("/staff/presence/delete",
     *    name="staff_presence_delete_by_array",
     *    methods={"HEAD", "DELETE"})
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
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the StaffPresence",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StaffPresenceType::class))
     *     )
     * )
     * @SWG\Tag(name="StaffPresence")
     */
    public function deleteByArray(Request $request)
    {
        $this->denyAccessUnlessGranted('staffPresenceDelete');

        $suppressedData = $this->staffPresenceService->deleteByArray($request->getContent());

        return new JsonResponse($suppressedData);
    }
}
