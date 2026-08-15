<?php

namespace App\Controller;

use App\Entity\Sport;
use App\Form\SportType;
use App\Service\SportServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * SportController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SportController extends AbstractController
{
    private $sportService;

    public function __construct(SportServiceInterface $sportService)
    {
        $this->sportService = $sportService;
    }

//LIST
    /**
     * Lists all the categories
     *
     * @Route("/sport/list",
     *    name="sport_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Sport::class))
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
     * @SWG\Tag(name="Sport")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
       // $this->denyAccessUnlessGranted('sportList');

        $categories = $paginator->paginate(
            $this->sportService->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $categoriesArray = array();
        foreach ($categories->getItems() as $sport) {
            $categoriesArray[] = $this->sportService->toArray($sport);
        };

        return new JsonResponse($categoriesArray);
    }

//SEARCH
    /**
     * Searches for %{term}% in name_fr for Sport
     *
     * @Route("/sport/search/{term}",
     *    name="sport_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Sport::class))
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
        $this->denyAccessUnlessGranted('sportList');

        $categories = $paginator->paginate(
            $this->sportService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $categoriesArray = array();
        foreach ($categories->getItems() as $sport) {
            $categoriesArray[] = $this->sportService->toArray($sport);
        };

        return new JsonResponse($categoriesArray);
    }

//DISPLAY
    /**
     * Displays sport
     *
     * @Route("/sport/display/{sportId}",
     *    name="sport_display",
     *    requirements={"sportId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("sport", expr="repository.findOneById(sportId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Sport::class)
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
     *     name="sportId",
     *     in="path",
     *     description="Id of the sport",
     *     type="integer",
     * )
     * @SWG\Tag(name="Sport")
     */
    public function display(Sport $sport)
    {
        $this->denyAccessUnlessGranted('sportDisplay', $sport);

        $sportArray = $this->sportService->toArray($sport);

        return new JsonResponse($sportArray);
    }

//CREATE
    /**
     * Creates sport
     *
     * @Route("/sport/create",
     *    name="sport_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="sport", ref=@Model(type=Sport::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Sport",
     *     required=true,
     *     @Model(type=SportType::class)
     * )
     * @SWG\Tag(name="Sport")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('sportCreate');

        $createdData = $this->sportService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies sport
     *
     * @Route("/sport/modify/{sportId}",
     *    name="sport_modify",
     *    requirements={"sportId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("sport", expr="repository.findOneById(sportId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="sport", ref=@Model(type=Sport::class)),
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
     *     name="sportId",
     *     in="path",
     *     description="Id for the sport",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Sport",
     *     required=true,
     *     @Model(type=SportType::class)
     * )
     * @SWG\Tag(name="Sport")
     */
    public function modify(Request $request, Sport $sport)
    {
        $this->denyAccessUnlessGranted('sportModify', $sport);

        $modifiedData = $this->sportService->modify($sport, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes sport
     *
     * @Route("/sport/delete/{sportId}",
     *    name="sport_delete",
     *    requirements={"sportId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("sport", expr="repository.findOneById(sportId)")
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
     *     name="sportId",
     *     in="path",
     *     description="Id for the sport",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Sport")
     */
    public function delete(Sport $sport)
    {
        $this->denyAccessUnlessGranted('sportDelete', $sport);

        $suppressedData = $this->sportService->delete($sport);

        return new JsonResponse($suppressedData);
    }
}
