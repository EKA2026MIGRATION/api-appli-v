<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\FoodType;
use App\Service\FoodServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * FoodController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class FoodController extends AbstractController
{
    private $foodService;

    public function __construct(FoodServiceInterface $foodService)
    {
        $this->foodService = $foodService;
    }

//LIST BY STATUS
    /**
     * Lists all the foods by status
     *
     * @Route("/food/list/{status}",
     *    name="food_list_status",
     *    requirements={
     *        "status": "^(active|disabled|archived)$"
     *    },
     *    defaults={"status": "active"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Food::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="active|disabled|archived foods",
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
     * @SWG\Tag(name="Food")
     */
    public function listByStatus(Request $request, PaginatorInterface $paginator, $status)
    {
        //$this->denyAccessUnlessGranted('foodList');

        $foods = $paginator->paginate(
            $this->foodService->findAllByStatus($status),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $foodsArray = array();
        foreach ($foods->getItems() as $food) {
            $foodsArray[] = $this->foodService->toArray($food);
        };

        return new JsonResponse($foodsArray);
    }

//DISPLAY
    /**
     * Displays the food using its id
     *
     * @Route("/food/display/{foodId}",
     *    name="food_display_id",
     *    methods={"HEAD", "GET"})
     * @Entity("food", expr="repository.findOneById(foodId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Food::class))
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
     *     name="foodId",
     *     in="path",
     *     description="Id for the food",
     *     type="string",
     * )
     * @SWG\Tag(name="Food")
     */
    public function display(Food $food)
    {
        $this->denyAccessUnlessGranted('foodDisplay', $food);

        $foodArray = $this->foodService->toArray($food);

        return new JsonResponse($foodArray);
    }

//CREATE
    /**
     * Creates a Food
     *
     * @Route("/food/create",
     *    name="food_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="food", ref=@Model(type=Food::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Food",
     *     required=true,
     *     @Model(type=FoodType::class)
     * )
     * @SWG\Tag(name="Food")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('foodCreate');

        $createdData = $this->foodService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies food
     *
     * @Route("/food/modify/{foodId}",
     *    name="food_modify",
     *    requirements={"foodId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("food", expr="repository.findOneById(foodId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="food", ref=@Model(type=Food::class)),
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
     *     name="foodId",
     *     in="path",
     *     required=true,
     *     description="Id of the food",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Food",
     *     required=true,
     *     @Model(type=FoodType::class)
     * )
     * @SWG\Tag(name="Food")
     */
    public function modify(Request $request, Food $food)
    {
        $this->denyAccessUnlessGranted('foodModify', $food);

        $modifiedData = $this->foodService->modify($food, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes food
     *
     * @Route("/food/delete/{foodId}",
     *    name="food_delete",
     *    requirements={"foodId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("food", expr="repository.findOneById(foodId)")
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
     *     name="foodId",
     *     in="path",
     *     required=true,
     *     description="Id of the food",
     *     type="integer",
     * )
     * @SWG\Tag(name="Food")
     */
    public function delete(Food $food)
    {
        $this->denyAccessUnlessGranted('foodDelete', $food);

        $suppressedData = $this->foodService->delete($food);

        return new JsonResponse($suppressedData);
    }
}
