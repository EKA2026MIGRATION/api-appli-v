<?php

namespace App\Controller;

use App\Entity\HistoricSms;
use App\Form\HistoricSmsType;
use App\Service\HistoricSmsServiceInterface;
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
class HistoricSmsController extends AbstractController
{
    private $historicSmsService;

    public function __construct(HistoricSmsServiceInterface $historicSmsService)
    {
        $this->historicSmsService = $historicSmsService;
    }

//LIST
    /**
     * Lists all the extract list
     *
     * @Route("/historicSms/list/{status}",
     *    name="historic_sms_list",
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
     * @SWG\Tag(name="HistoricSms")
     */
    public function listAll(Request $request, $status = null)
    {

        $historicSmss = $this->historicSmsService->findAll($status); 
        return new JsonResponse($historicSmss);
    }

//DISPLAY
    /**
     * Displays historicSms
     *
     * @Route("/historicSms/display/{id}",
     *    name="historicSms_dsplay",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("HistoricSms", expr="repository.findOneById(id)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=HistoricSms::class)
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
     *     name="id",
     *     in="path",
     *     description="Id of the ExtractList",
     *     type="integer",
     * )
     * @SWG\Tag(name="HistoricSms")
     */
    public function display(HistoricSms $historicSms)
    {

        $historicSms = $this->historicSmsService->toArray($historicSms);

        return new JsonResponse($historicSms);
    }

//DISPLAY
    /**
     * Displays historicSms
     *
     * @Route("/historicSms/displayAll/{id}/{status}/{limit}",
     *    name="historicSms_dsplay_all",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("HistoricSms", expr="repository.findOneById(id)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=HistoricSms::class)
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
     *     name="id",
     *     in="path",
     *     description="Id of the ExtractList",
     *     type="integer",
     * )
     * @SWG\Tag(name="HistoricSms")
     */
    public function displayAll(HistoricSms $historicSms, $status = null, $limit = null)
    {

        if($status == "notSent" && $limit == null) $limit = 200;

        $historicSms = $this->historicSmsService->displayAll($historicSms, $status, $limit);

        return new JsonResponse($historicSms);
    }


//DUPLICATE
    /**
     * duplicate historicSms
     *
     * @Route("/historicSms/duplicate/{id}",
     *    name="historicSms_duplicate",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("HistoricSms", expr="repository.findOneById(id)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=HistoricSms::class)
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
     *     name="id",
     *     in="path",
     *     description="Id of the ExtractList",
     *     type="integer",
     * )
     * @SWG\Tag(name="HistoricSms")
     */
    public function duplicate(HistoricSms $historicSms)
    {
        $historicSms = $this->historicSmsService->duplicate($historicSms);
        return new JsonResponse($historicSms);
    }


//CREATE
    /**
     * Creates historicSms
     *
     * @Route("/historicSms/create",
     *    name="historicSms_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="ExtractList", ref=@Model(type=HistoricSms::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the historicSMs",
     *     required=true,
     *     @Model(type=HistoricSmsType::class)
     * )
     * @SWG\Tag(name="HistoricSms")
     */
    public function create(Request $request)
    {
        $createdData = $this->historicSmsService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies historicSms
     *
     * @Route("/historicSms/modify/{id}",
     *    name="historicSms_modify",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("HistoricSms", expr="repository.findOneById(id)")
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
     * @SWG\Tag(name="HistoricSms")
     */
    public function modify(Request $request, HistoricSms $historicSms)
    {

        $modifiedData = $this->historicSmsService->modify($historicSms, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes historicSms
     *
     * @Route("/historicSms/delete/{id}",
     *    name="historicSms_delete",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("HistoricSms", expr="repository.findOneById(id)")
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
     * @SWG\Tag(name="HistoricSms")
     */
    public function delete(HistoricSms $historicSms)
    {

        $suppressedData = $this->historicSmsService->delete($historicSms);

        return new JsonResponse($suppressedData);
    }
}
