<?php

namespace App\Controller;

use App\Entity\Week;
use App\Form\WeekType;
use App\Service\WeekServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * WeekController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class WeekController extends AbstractController
{
    private $weekService;

    public function __construct(WeekServiceInterface $weekService)
    {
        $this->weekService = $weekService;
    }

//LIST
    /**
     * Lists all the weeks
     *
     * @Route("/week/list",
     *    name="week_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Week::class)),
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
     * @SWG\Tag(name="Week")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('weekList');

        $weeks = $paginator->paginate(
            $this->weekService->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $weeksArray = array();
        foreach ($weeks->getItems() as $week) {
            $weeksArray[] = $this->weekService->toArray($week);
        };

        return new JsonResponse($weeksArray);
    }

//SEARCH
    /**
     * Searches for %{term}% in name for Week
     *
     * @Route("/week/search/{term}",
     *    name="week_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Week::class))
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
     *     name="term",
     *     in="path",
     *     required=true,
     *     description="Searched term",
     *     type="string",
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
     * @SWG\Tag(name="Child")
     */
    public function search(Request $request, PaginatorInterface $paginator, string $term)
    {
        $this->denyAccessUnlessGranted('weekList');

        $weeks = $paginator->paginate(
            $this->weekService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $weeksArray = array();
        foreach ($weeks->getItems() as $week) {
            $weeksArray[] = $this->weekService->toArray($week);
        };

        return new JsonResponse($weeksArray);
    }

//DISPLAY
    /**
     * Displays week
     *
     * @Route("/week/display/{weekId}",
     *    name="week_display",
     *    requirements={"weekId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("week", expr="repository.findOneById(weekId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Week::class)
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
     *     name="weekId",
     *     in="path",
     *     description="Id of the week",
     *     type="integer",
     * )
     * @SWG\Tag(name="Week")
     */
    public function display(Week $week)
    {
        $this->denyAccessUnlessGranted('weekDisplay', $week);

        $weekArray = $this->weekService->toArray($week);

        return new JsonResponse($weekArray);
    }

//CREATE
    /**
     * Creates week
     *
     * @Route("/week/create",
     *    name="week_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="week", ref=@Model(type=Week::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Week",
     *     required=true,
     *     @Model(type=WeekType::class)
     * )
     * @SWG\Tag(name="Week")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('weekCreate');

        $createdData = $this->weekService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies week
     *
     * @Route("/week/modify/{weekId}",
     *    name="week_modify",
     *    requirements={"weekId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("week", expr="repository.findOneById(weekId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="week", ref=@Model(type=Week::class)),
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
     *     name="weekId",
     *     in="path",
     *     description="Id for the week",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Week",
     *     required=true,
     *     @Model(type=WeekType::class)
     * )
     * @SWG\Tag(name="Week")
     */
    public function modify(Request $request, Week $week)
    {
        $this->denyAccessUnlessGranted('weekModify', $week);

        $modifiedData = $this->weekService->modify($week, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes week
     *
     * @Route("/week/delete/{weekId}",
     *    name="week_delete",
     *    requirements={"weekId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("week", expr="repository.findOneById(weekId)")
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
     *     name="weekId",
     *     in="path",
     *     description="Id for the week",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Week")
     */
    public function delete(Week $week)
    {
        $this->denyAccessUnlessGranted('weekDelete', $week);

        $suppressedData = $this->weekService->delete($week);

        return new JsonResponse($suppressedData);
    }
}
