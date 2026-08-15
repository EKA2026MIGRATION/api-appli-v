<?php

namespace App\Controller;

use App\Entity\HistoricPersonAction;
use App\Service\HistoricPersonActionServiceInterface;
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
class HistoricPersonActionController extends AbstractController
{
    private $historicPersonActionService;

    public function __construct(HistoricPersonActionServiceInterface $historicPersonActionService)
    {
        $this->historicPersonActionService = $historicPersonActionService;
    }


//create
    /**
     * Modifies historicSms
     *
     * @Route("/historicPersonAction/create",
     *    name="historicPersonAction_create",
     *    methods={"HEAD", "POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="extractList", ref=@Model(type=HistoricSmsList::class)),
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
     * @SWG\Tag(name="HistoricPersonAction")
     */
    public function create(Request $request) {
    
        $result = $this->historicPersonActionService->create($request->getContent());
        return new JsonResponse($result);
    }

    //List
    /**
     * List historicPersonAction
     *
     * @Route("/historicPersonAction/listByAction/{action}",
     *    name="historicPersonAction_list_by_action",
     *    methods={"HEAD", "GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="extractList", ref=@Model(type=HistoricSmsList::class)),
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
     * @SWG\Tag(name="HistoricSmsList")
     */
    public function listByAction($action) {
    
        $result = $this->historicPersonActionService->listByAction($action);
        return new JsonResponse($result);
    }

}
