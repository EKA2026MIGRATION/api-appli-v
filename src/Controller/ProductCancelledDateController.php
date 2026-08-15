<?php

namespace App\Controller;

use App\Entity\ProductCancelledDate;
use App\Form\ProductCancelledDateType;
use App\Service\ProductCancelledDateServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ProductCancelledDateController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductCancelledDateController extends AbstractController
{
    private $productCancelledDateService;

    public function __construct(ProductCancelledDateServiceInterface $productCancelledDateService)
    {
        $this->productCancelledDateService = $productCancelledDateService;
    }

//LIST
    /**
     * Lists all the productCancelledDate
     *
     * @Route("/product-cancelled-date/list",
     *    name="product_cancelled_date_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ProductCancelledDate::class))
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
     * @SWG\Tag(name="ProductCancelledDate")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        //$this->denyAccessUnlessGranted('productCancelledDateList');

        $productCancelledDatesArray = $this->productCancelledDateService->findCurrent();

        return new JsonResponse($productCancelledDatesArray);
    }

//LIST BY DATE
    /**
     * Lists all the productCancelledDate for a specific date
     *
     * @Route("/product-cancelled-date/list/{date}",
     *    name="product_cancelled_date_list_date",
     *    requirements={"date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ProductCancelledDate::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the productCancelledDate (YYYY-MM-DD | YYYY-MM | YYYY)",
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
     * @SWG\Tag(name="ProductCancelledDate")
     */
    public function listAllDate(Request $request, PaginatorInterface $paginator, $date)
    {
        //$this->denyAccessUnlessGranted('productCancelledDateList');

        $productCancelledDates = $paginator->paginate(
            $this->productCancelledDateService->findAllByDate($date),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $productCancelledDatesArray = array();
        foreach ($productCancelledDates->getItems() as $productCancelledDate) {
            $productCancelledDatesArray[] = $this->productCancelledDateService->toArray($productCancelledDate);
        };

        return new JsonResponse($productCancelledDatesArray);
    }

//LIST BY CATEGORY AND DATE
    /**
     * Lists all the productCancelledDate for a specific category and date
     *
     * @Route("product-cancelled-date/list-category/{categoryId}/{date}",
     *    name="product_cancelled_date_list_category_date",
     *    requirements={
     *        "categoryId": "^([0-9]+)$",
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ProductCancelledDate::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="categoryId",
     *     in="path",
     *     description="Id of the category",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the productCancelledDate (YYYY-MM-DD | YYYY-MM)",
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
     * @SWG\Tag(name="ProductCancelledDate")
     */
    public function listAllCategoryDate(Request $request, PaginatorInterface $paginator, $categoryId, $date)
    {
        //$this->denyAccessUnlessGranted('productCancelledDateList');

        $productCancelledDates = $paginator->paginate(
            $this->productCancelledDateService->findAllByCategoryDate($categoryId, $date),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $productCancelledDatesArray = array();
        foreach ($productCancelledDates->getItems() as $productCancelledDate) {
            $productCancelledDatesArray[] = $this->productCancelledDateService->toArray($productCancelledDate);
        };

        return new JsonResponse($productCancelledDatesArray);
    }

//LIST BY PRODUCT AND DATE
    /**
     * Lists all the productCancelledDate for a specific product and date
     *
     * @Route("product-cancelled-date/list-product/{productId}/{date}",
     *    name="product_cancelled_date_list_product_date",
     *    requirements={
     *        "productId": "^([0-9]+)$",
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ProductCancelledDate::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="productId",
     *     in="path",
     *     description="Id of the product",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the productCancelledDate (YYYY-MM-DD | YYYY-MM)",
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
     * @SWG\Tag(name="ProductCancelledDate")
     */
    public function listAllProductDate(Request $request, PaginatorInterface $paginator, $productId, $date)
    {
        //$this->denyAccessUnlessGranted('productCancelledDateList');

        $productCancelledDates = $paginator->paginate(
            $this->productCancelledDateService->findAllByProductDate($productId, $date),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $productCancelledDatesArray = array();
        foreach ($productCancelledDates->getItems() as $productCancelledDate) {
            $productCancelledDatesArray[] = $this->productCancelledDateService->toArray($productCancelledDate);
        };

        return new JsonResponse($productCancelledDatesArray);
    }

//DISPLAY
    /**
     * Displays productCancelledDate using productCancelledDateId
     *
     * @Route("/product-cancelled-date/display/{productCancelledDateId}",
     *    name="product_cancelled_date_display",
     *    requirements={"productCancelledDateId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("productCancelledDate", expr="repository.findOneById(productCancelledDateId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ProductCancelledDate::class))
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
     *     name="productCancelledDateId",
     *     in="path",
     *     required=true,
     *     description="Id of the productCancelledDate",
     *     type="integer",
     * )
     * @SWG\Tag(name="ProductCancelledDate")
     */
    public function display(ProductCancelledDate $productCancelledDate)
    {
        $this->denyAccessUnlessGranted('productCancelledDateDisplay', $productCancelledDate);

        $productCancelledDateArray = $this->productCancelledDateService->toArray($productCancelledDate);

        return new JsonResponse($productCancelledDateArray);
    }

//CREATE
    /**
     * Creates ProductCancelledDate
     *
     * @Route("/product-cancelled-date/create",
     *    name="product_cancelled_date_create",
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
     *     description="Data for the ProductCancelledDate",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ProductCancelledDateType::class))
     *     )
     * )
     * @SWG\Tag(name="ProductCancelledDate")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('productCancelledDateCreate');

        $createdData = $this->productCancelledDateService->create($request->getContent());

        return new JsonResponse($createdData);
    }

    /**
     * @Route("/product-cancelled-date/create-by-category/{category_name}",
     *     name="product_cancelled_date_create_by_category",
     *    methods={"HEAD", "POST"})
     *
 */
    public function createByCategory(Request $request, $category_name = null)
    {
        $this->denyAccessUnlessGranted('productCancelledDateCreate');

        $createdData = $this->productCancelledDateService->createByCategory($request->getContent(), $category_name);

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies productCancelledDate
     *
     * @Route("/product-cancelled-date/modify/{productCancelledDateId}",
     *    name="product_cancelled_date_modify",
     *    requirements={"productCancelledDateId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("productCancelledDate", expr="repository.findOneById(productCancelledDateId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="productCancelledDate", ref=@Model(type=ProductCancelledDate::class)),
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
     *     name="productCancelledDateId",
     *     in="path",
     *     description="Id for the productCancelledDate",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the ProductCancelledDate",
     *     required=true,
     *     @Model(type=ProductCancelledDateType::class)
     * )
     * @SWG\Tag(name="ProductCancelledDate")
     */
    public function modify(Request $request, ProductCancelledDate $productCancelledDate)
    {
        $this->denyAccessUnlessGranted('productCancelledDateModify', $productCancelledDate);

        $modifiedData = $this->productCancelledDateService->modify($productCancelledDate, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes productCancelledDate using its id
     *
     * @Route("/product-cancelled-date/delete/{productCancelledDateId}",
     *    name="product_cancelled_date_delete",
     *    requirements={"productCancelledDateId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("component", expr="repository.findOneById(productCancelledDateId)")
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
     *     name="productCancelledDateId",
     *     in="path",
     *     description="Id for the ProductCancelledDate",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="ProductCancelledDate")
     */
    public function delete(ProductCancelledDate $productCancelledDate)
    {
        $this->denyAccessUnlessGranted('productCancelledDateDelete', $productCancelledDate);

        $suppressedData = $this->productCancelledDateService->delete($productCancelledDate);

        return new JsonResponse($suppressedData);
    }
}
