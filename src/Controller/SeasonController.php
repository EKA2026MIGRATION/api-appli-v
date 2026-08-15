<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Service\SeasonServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * SeasonController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SeasonController extends AbstractController
{
    private $seasonService;

    public function __construct(SeasonServiceInterface $seasonService)
    {
        $this->seasonService = $seasonService;
    }

//LIST BY STATUS
    /**
     * Lists all the seasons by status
     *
     * @Route("/season/list/{status}",
     *    name="season_list_status",
     *    requirements={
     *        "status": "^(active|disabled|archived|draft)$"
     *    },
     *    defaults={"status": "active"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Season::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="active|disabled|archived seasons",
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
     * @SWG\Tag(name="Season")
     */
    public function listByStatus(Request $request, PaginatorInterface $paginator, $status)
    {
      //  $this->denyAccessUnlessGranted('seasonList');

        $seasons = $paginator->paginate(
            $this->seasonService->findAllByStatus($status),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $seasonsArray = array();
        foreach ($seasons->getItems() as $season) {
            $seasonsArray[] = $this->seasonService->toArray($season);
        };

        return new JsonResponse($seasonsArray);
    }

//DISPLAY
    /**
     * Displays season
     *
     * @Route("/season/display/{seasonId}",
     *    name="season_display",
     *    requirements={"seasonId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("season", expr="repository.findOneById(seasonId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Season::class),
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
     * @SWG\Tag(name="Season")
     */
    public function display(Request $request, Season $season)
    {
        $this->denyAccessUnlessGranted('seasonDisplay', $season);

        $seasonArray = $this->seasonService->toArray($season);

        return new JsonResponse($seasonArray);
    }

//CREATE
    /**
     * Creates a season
     *
     * @Route("/season/create",
     *    name="season_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="season", ref=@Model(type=Season::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Season",
     *     required=true,
     *     @Model(type=SeasonType::class)
     * )
     * @SWG\Tag(name="Season")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('seasonCreate');

        $createdData = $this->seasonService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies season
     *
     * @Route("/season/modify/{seasonId}",
     *    name="season_modify",
     *    requirements={"seasonId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("season", expr="repository.findOneById(seasonId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="season", ref=@Model(type=Season::class)),
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
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Season",
     *     required=true,
     *     @Model(type=SeasonType::class)
     * )
     * @SWG\Tag(name="Season")
     */
    public function modify(Request $request, Season $season)
    {
        $this->denyAccessUnlessGranted('seasonModify', $season);

        $modifiedData = $this->seasonService->modify($season, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes season
     *
     * @Route("/season/delete/{seasonId}",
     *    name="season_delete",
     *    requirements={"seasonId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("season", expr="repository.findOneById(seasonId)")
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
     *     name="seasonId",
     *     in="path",
     *     required=true,
     *     description="Id of the season",
     *     type="integer",
     * )
     * @SWG\Tag(name="Season")
     */
    public function delete(Season $season)
    {
        $this->denyAccessUnlessGranted('seasonDelete', $season);

        $suppressedData = $this->seasonService->delete($season);

        return new JsonResponse($suppressedData);
    }
}
