<?php

namespace App\Controller;

use App\Entity\ExtractList;
use App\Form\ExtractListType;
use App\Service\ExtractListServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ExtractListController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class ExtractListController extends AbstractController
{
    private $extractListService;

    public function __construct(ExtractListServiceInterface $extractListService)
    {
        $this->extractListService = $extractListService;
    }

//LIST
    /**
     * Lists all the extract list
     *
     * @Route("/extractList/list",
     *    name="extract_list_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ExtractList::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="ExtractList")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {

        $extractLists = $this->extractListService->findAll(); 
        return new JsonResponse($extractLists);
    }

//DISPLAY
    /**
     * Displays ExtractList
     *
     * @Route("/extractList/display/{id}",
     *    name="extractList_display",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("ExtractList", expr="repository.findOneById(id)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=ExtractList::class)
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
     *     name="ExtractListId",
     *     in="path",
     *     description="Id of the ExtractList",
     *     type="integer",
     * )
     * @SWG\Tag(name="ExtractList")
     */
    public function display(ExtractList $extractList)
    {

        $extractList = $this->extractListService->toArray($extractList);

        return new JsonResponse($extractList);
    }

//LIST EXTRACT CONTENT
    /**
     * Displays ExtractList
     *
     * @Route("/extractList/listExecuteContent/{id}/{type}",
     *    name="extractList_listExecuteContent",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("ExtractList", expr="repository.findOneById(id)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=ExtractList::class)
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
     *     name="ExtractListId",
     *     in="path",
     *     description="Id of the ExtractList",
     *     type="integer",
     * )
     * @SWG\Tag(name="ExtractList")
     */
    public function listExecuteContent(ExtractList $extractList, $type = "sms") {
        $listResult = $this->extractListService->listExecuteContent($extractList, $type);

        return new JsonResponse($listResult);
    }

//CREATE
    /**
     * Creates extractList
     *
     * @Route("/extractList/create",
     *    name="extractList_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="ExtractList", ref=@Model(type=ExtractList::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the ExtractList",
     *     required=true,
     *     @Model(type=ExtractListType::class)
     * )
     * @SWG\Tag(name="ExtractList")
     */
    public function create(Request $request)
    {

        $createdData = $this->extractListService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies ExtractList
     *
     * @Route("/extractList/modify/{id}",
     *    name="ExtractList_modify",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("ExtractList", expr="repository.findOneById(id)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="extractList", ref=@Model(type=ExtractList::class)),
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
     * @SWG\Tag(name="ExtractList")
     */
    public function modify(Request $request, ExtractList $extractList)
    {

        $modifiedData = $this->extractListService->modify($extractList, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes ExtractList
     *
     * @Route("/extractList/delete/{id}",
     *    name="extractList_delete",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("ExtractList", expr="repository.findOneById(id)")
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
     * @SWG\Tag(name="ExtractList")
     */
    public function delete(ExtractList $extractList)
    {

        $suppressedData = $this->extractListService->delete($extractList);

        return new JsonResponse($suppressedData);
    }
}
