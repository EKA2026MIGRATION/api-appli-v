<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\ProductServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ProductController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductController extends AbstractController
{
    private $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

//LIST
    /**
     * Lists all the products
     *
     * @Route("/product/list/{visibility}/{family_id}",
     *    name="product_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class))
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
     * @SWG\Tag(name="Product")
     */
    public function listAll(Request $request, PaginatorInterface $paginator, $visibility = null, $family_id = null)
    {
      //  $this->denyAccessUnlessGranted('productList');
        $productArray = [];
        $productsArray = $this->productService->findAllActiveProducts($visibility, $family_id);


        return new JsonResponse($productsArray);
    }

//LIST FOR A CHILD
    /**
     * Lists all the products linked to a Child
     *
     * @Route("/product/list/child/{childId}",
     *    name="product_list_by_child",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class))
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
     * @SWG\Tag(name="Product")
     */
    public function listAllByChild(Request $request, PaginatorInterface $paginator, $childId)
    {
        $this->denyAccessUnlessGranted('productList');

        $products = $paginator->paginate(
            $this->productService->findAllByChild($childId),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $productsArray = array();
        foreach ($products->getItems() as $product) {
            $productsArray[] = $this->productService->toArray($product);
        };

        return new JsonResponse($productsArray);
    }

    //LIST
    /**
     * Lists all the products
     *
     * @Route("/product/update/frontMenu",
     *    name="product_front_menu",
     *    methods={"HEAD", "PUT"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Product")
     */
    public function updateProductFrontMenu(Request $request)
    {
        $this->denyAccessUnlessGranted('productList');
        $result= $this->productService->updateProductFrontMenu($request->getContent());
        return new JsonResponse($result);
    }




//SEARCH
    /**
     * Searches for %{term}% in name_fr for Product
     *
     * @Route("/product/search/{term}",
     *    name="product_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class))
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
     * @SWG\Tag(name="Product")
     */
    public function search(Request $request, PaginatorInterface $paginator, string $term)
    {
        $this->denyAccessUnlessGranted('productList');

        $products = $paginator->paginate(
            $this->productService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $productsArray = array();
        foreach ($products->getItems() as $product) {
            $productsArray[] = $this->productService->toArray($product);
        };

        return new JsonResponse($productsArray);
    }

//DISPLAY
    /**
     * Displays product
     *
     * @Route("/product/display/{productId}",
     *    name="product_display",
     *    requirements={"productId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("product", expr="repository.findOneById(productId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Product::class)
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
     *     name="productId",
     *     in="path",
     *     description="Id of the product",
     *     type="integer",
     * )
     * @SWG\Tag(name="Product")
     */
    public function display(Product $product)
    {
        $this->denyAccessUnlessGranted('productDisplay', $product);

        $productArray = $this->productService->toArray($product);

        return new JsonResponse($productArray);
    }


//DISPLAY
    /**
     * Displays product
     *
     * @Route("/product/productPersonal/{childId}",
     *    name="display_product_personal",
     *    requirements={"childId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("product", expr="repository.findOneById(productId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Product::class)
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
     *     name="childId",
     *     in="path",
     *     description="Id of the child",
     *     type="integer",
     * )
     * @SWG\Tag(name="Product")
     */
    public function displayProductPersonal($childId)
    {

        $productArray = $this->productService->findProductPersonal($childId);

        return new JsonResponse($productArray);
    }


//DISPLAY
    /**
     * Displays product
     *
     * @Route("/product/registrations/{productId}",
     *    name="product_registrations",
     *    requirements={"productId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("product", expr="repository.findOneById(productId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Product::class)
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
     *     name="productId",
     *     in="path",
     *     description="Id of the product",
     *     type="integer",
     * )
     * @SWG\Tag(name="Product")
     */
    public function registrations(Product $product)
    {
        $this->denyAccessUnlessGranted('productDisplay', $product);

        $registrationsListChilds = $this->productService->getRegistrationsByProduct($product);

        return new JsonResponse($registrationsListChilds);
    }


//CREATE
    /**
     * Creates product
     *
     * @Route("/product/create",
     *    name="product_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="product", ref=@Model(type=Product::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Product",
     *     required=true,
     *     @Model(type=ProductType::class)
     * )
     * @SWG\Tag(name="Product")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('productCreate');

        $createdData = $this->productService->create($request->getContent());

        return new JsonResponse($createdData);
    }


//FAST UPDATE
    /**
     * Creates product
     *
     * @Route("/product/fastUpdate",
     *    name="product_fastUpdate",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="product", ref=@Model(type=Product::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Product",
     *     required=true,
     *     @Model(type=ProductType::class)
     * )
     * @SWG\Tag(name="Product")
     */
    public function fastUpdate(Request $request)
    {
        $this->denyAccessUnlessGranted('productCreate');

        $createdData = $this->productService->fastUpdate($request->getContent());

        return new JsonResponse($createdData);
    }
//MODIFY
    /**
     * Modifies product
     *
     * @Route("/product/modify/{productId}",
     *    name="product_modify",
     *    requirements={"productId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("product", expr="repository.findOneById(productId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="product", ref=@Model(type=Product::class)),
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
     *     name="productId",
     *     in="path",
     *     description="Id for the product",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Product",
     *     required=true,
     *     @Model(type=ProductType::class)
     * )
     * @SWG\Tag(name="Product")
     */
    public function modify(Request $request, Product $product)
    {
        $this->denyAccessUnlessGranted('productModify', $product);

        $modifiedData = $this->productService->modify($product, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes product
     *
     * @Route("/product/delete/{productId}",
     *    name="product_delete",
     *    requirements={"productId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("product", expr="repository.findOneById(productId)")
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
     *     name="productId",
     *     in="path",
     *     description="Id for the product",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Product")
     */
    public function delete(Product $product)
    {
        $this->denyAccessUnlessGranted('productDelete', $product);

        $suppressedData = $this->productService->delete($product);

        return new JsonResponse($suppressedData);
    }
}
