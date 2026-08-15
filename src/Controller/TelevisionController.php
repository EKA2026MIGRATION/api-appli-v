<?php

namespace App\Controller;

use App\Entity\Television;
use App\Form\TelevisionType;
use App\Service\TelevisionServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TelevisionController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class TelevisionController extends AbstractController
{
    private $televisionService;

    public function __construct(TelevisionServiceInterface $televisionService)
    {
        $this->televisionService = $televisionService;
    }

//LIST
    /**
     * Lists all the television
     *
     * @Route("/television/list",
     *    name="television_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Television::class))
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
     * @SWG\Tag(name="Television")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('televisionList');

        $televisions = $paginator->paginate(
            $this->televisionService->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $televisionsArray = array();
        foreach ($televisions->getItems() as $television) {
            $televisionsArray[] = $this->televisionService->toArray($television);
        };

        return new JsonResponse($televisionsArray);
    }

//DISPLAY
    /**
     * Displays television using televisionId
     *
     * @Route("/television/display/{televisionId}",
     *    name="television_display",
     *    requirements={"televisionId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("television", expr="repository.findOneById(televisionId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Television::class))
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
     *     name="televisionId",
     *     in="path",
     *     required=true,
     *     description="Id of the television",
     *     type="integer",
     * )
     * @SWG\Tag(name="Television")
     */
    public function display(Television $television)
    {
        $this->denyAccessUnlessGranted('televisionDisplay', $television);

        $televisionArray = $this->televisionService->toArray($television);

        return new JsonResponse($televisionArray);
    }

//CREATE
    /**
     * Creates Television
     *
     * @Route("/television/create",
     *    name="television_create",
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
     *     description="Data for the Television",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TelevisionType::class))
     *     )
     * )
     * @SWG\Tag(name="Television")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('televisionCreate');

        $createdData = $this->televisionService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies television
     *
     * @Route("/television/modify/{televisionId}",
     *    name="television_modify",
     *    requirements={"televisionId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("television", expr="repository.findOneById(televisionId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="television", ref=@Model(type=Television::class)),
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
     *     name="televisionId",
     *     in="path",
     *     required=true,
     *     description="Id of the Television",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Television",
     *     required=true,
     *     @Model(type=TelevisionType::class)
     * )
     * @SWG\Tag(name="Television")
     */
    public function modify(Request $request, Television $television)
    {
        $this->denyAccessUnlessGranted('televisionModify', $television);

        $modifiedData = $this->televisionService->modify($television, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes television using its id
     *
     * @Route("/television/delete/{televisionId}",
     *    name="television_delete",
     *    requirements={"televisionId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("component", expr="repository.findOneById(televisionId)")
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
     *     name="televisionId",
     *     in="path",
     *     description="Id for the Television",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Television")
     */
    public function delete(Television $television)
    {
        $this->denyAccessUnlessGranted('televisionDelete', $television);

        $suppressedData = $this->televisionService->delete($television);

        return new JsonResponse($suppressedData);
    }
}
