<?php

namespace App\Controller;

use App\Entity\StockProduct;
use App\Service\StockProductServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * StockProductController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StockProductController extends AbstractController
{
    private $stockProductService;

    public function __construct(StockProductServiceInterface $stockProductService)
    {
        $this->stockProductService = $stockProductService;
    }

//LIST
    /**
     * Displays list rdv with date optionnel
     *
     * @Route("/stockProduct/list",
     *    name="stockProduct_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StockProduct::class))
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
     *     name="date",
     *     in="path",
     *     description="StockProduct by list",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Tag(name="StockProduct")
     */
    public function list()
    {
        $stockProductsArray = $this->stockProductService->findAll();
        return new JsonResponse($stockProductsArray);
    }

    /**
     * display list product in alert stock
     * @Route("/stockProduct/alert", name="stockProduct_alert", methods={"HEAD", "GET"})
     * @SWG\Response ( response=200, description="Success", @SWG\Schema(type="array", @SWG\Items(ref=@Model(type=StockProduct::class))) )
     * @SWG\Response ( response=403, description="Access denied" )
     * @SWG\Response ( response=404, description="Not Found" )
     * @SWG\Tag(name="StockProduct")
     * @return JsonResponse
     */
    public function getAlert()
    {
        $stockProductsArray = $this->stockProductService->getAlert();
        return new JsonResponse($stockProductsArray);
    }

    //inventory
    /**
     * Displays list rdv with date optionnel
     *
     * @Route("/stockProduct/inventory/{date}",
     *    name="stockProduct_inventory_date",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StockProductInventory::class))
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
     *     name="date",
     *     in="path",
     *     description="StockProduct by list",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Tag(name="StockProduct")
     */
    public function inventory($date)
    {
        $stockProductsArray = $this->stockProductService->inventory($date);
        return new JsonResponse($stockProductsArray);
    }


    //date last inventory
    /**
     * Return last date inventory
     *
     * @Route("/stockProduct/inventory/latest/date",
     *    name="stockProduct_inventory_date_latest",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StockProductInventory::class))
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
     *     name="date",
     *     in="path",
     *     description="StockProduct by list",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Tag(name="StockProduct")
     */
    public function inventoryLatest()
    {
        $date= $this->stockProductService->latestDateInventory();
        return new JsonResponse($date);
    }

//MODIFY
    /**
     * Modifies Booklet
     *
     * @Route("/stockProduct/modify/{stockProductId}",
     *    name="stockProduct_modify",
     *    requirements={"stockProduct": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet", ref=@Model(type=StockProduct::class)),
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
     * @SWG\Tag(name="StockProduct")
     */
    public function modify(Request $request, $stockProductId)
    {

        $modifiedData = $this->stockProductService->modify($stockProductId, $request->getContent());

        return new JsonResponse($modifiedData);
    }

    /**
     * Modifies StockOrder
     *
     * @Route("/stockOrder/update/{stockProductId}",
     *    name="stockOrder_update",
     *    requirements={"stockProduct": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet", ref=@Model(type=StockProduct::class)),
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
     * @SWG\Tag(name="StockProduct")
     */
    public function update(Request $request, $stockProductId)
    {

        $modifiedData = $this->stockProductService->addStockOrder($stockProductId, $request->getContent());

        return new JsonResponse($modifiedData);
    }

    /**
     * Modifies StockOrder
     *
     * @Route("/stockOrder/listContent/{date}",
     *    name="stockOrder_content_list_by_date",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet", ref=@Model(type=StockProduct::class)),
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
     * @SWG\Tag(name="StockProduct")
     */
    public function listStockOrderContent($date)
    {

        $modifiedData = $this->stockProductService->listOrderDate($date);

        return new JsonResponse($modifiedData);
    }

    /**
     * Modifies StockOrder list
     *
     * @Route("/stockOrder/list",
     *    name="stockOrder_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet", ref=@Model(type=StockProduct::class)),
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
     * @SWG\Tag(name="StockProduct")
     */
    public function stockOrderList()
    {

        $modifiedData = $this->stockProductService->stockOrderList();

        return new JsonResponse($modifiedData);
    }


}
