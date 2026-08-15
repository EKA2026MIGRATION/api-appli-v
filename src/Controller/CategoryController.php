<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\CategoryServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CategoryController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class CategoryController extends AbstractController
{
    private $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

//LIST
    /**
     * Lists all the categories
     *
     * @Route("/category/listStandard",
     *    name="category_list_standard",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Category::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Category")
     */
    public function listStandard(Request $request)
    {
        $this->denyAccessUnlessGranted('categoryList');

        $categoriesArray = array();

        $categoriesArray = $this->categoryService->findAllStandard();

        return new JsonResponse($categoriesArray);
    }



//LIST
    /**
     * Lists all the categories
     *
     * @Route("/category/list",
     *    name="category_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Category::class))
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
     * @SWG\Tag(name="Category")
     */
    public function listAll(Request $request)
    {
      //  $this->denyAccessUnlessGranted('categoryList');
        ini_set('memory_limit', '4096M');
        ini_set('max_execution_time', 0);
        
        $categoriesArray = [];

        $categories = $this->categoryService->findAllActive();

        foreach ($categories as $category) {
            $categoriesArray[] = $this->categoryService->toArray($category);
        };

        return new JsonResponse($categoriesArray);
    }

//SEARCH
    /**
     * Searches for %{term}% in name_fr for Category
     *
     * @Route("/category/search/{term}",
     *    name="category_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Category::class))
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
        $this->denyAccessUnlessGranted('categoryList');

        $categories = $paginator->paginate(
            $this->categoryService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $categoriesArray = array();
        foreach ($categories->getItems() as $category) {
            $categoriesArray[] = $this->categoryService->toArray($category);
        };

        return new JsonResponse($categoriesArray);
    }

//DISPLAY
    /**
     * Displays category
     *
     * @Route("/category/display/{categoryId}",
     *    name="category_display",
     *    requirements={"categoryId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("category", expr="repository.findOneById(categoryId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Category::class)
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
     *     name="categoryId",
     *     in="path",
     *     description="Id of the category",
     *     type="integer",
     * )
     * @SWG\Tag(name="Category")
     */
    public function display(Category $category)
    {
        $this->denyAccessUnlessGranted('categoryDisplay', $category);

        $categoryArray = $this->categoryService->toArray($category);

        return new JsonResponse($categoryArray);
    }

//CREATE
    /**
     * Creates category
     *
     * @Route("/category/create",
     *    name="category_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="category", ref=@Model(type=Category::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Category",
     *     required=true,
     *     @Model(type=CategoryType::class)
     * )
     * @SWG\Tag(name="Category")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('categoryCreate');

        $createdData = $this->categoryService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies category
     *
     * @Route("/category/modify/{categoryId}",
     *    name="category_modify",
     *    requirements={"categoryId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("category", expr="repository.findOneById(categoryId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="category", ref=@Model(type=Category::class)),
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
     *     name="categoryId",
     *     in="path",
     *     description="Id for the category",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Category",
     *     required=true,
     *     @Model(type=CategoryType::class)
     * )
     * @SWG\Tag(name="Category")
     */
    public function modify(Request $request, Category $category)
    {
        $this->denyAccessUnlessGranted('categoryModify', $category);

        $modifiedData = $this->categoryService->modify($category, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes category
     *
     * @Route("/category/delete/{categoryId}",
     *    name="category_delete",
     *    requirements={"categoryId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("category", expr="repository.findOneById(categoryId)")
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
     *     name="categoryId",
     *     in="path",
     *     description="Id for the category",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Category")
     */
    public function delete(Category $category)
    {
        $this->denyAccessUnlessGranted('categoryDelete', $category);

        $suppressedData = $this->categoryService->delete($category);

        return new JsonResponse($suppressedData);
    }
}
