<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\MealType;
use App\Service\MealServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * MealController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class MealController extends AbstractController
{
    private $mealService;

    public function __construct(MealServiceInterface $mealService)
    {
        $this->mealService = $mealService;
    }

//LIST BY DATE
    /**
     * Lists all the meals for a specific date
     *
     * @Route("/meal/list/{date}",
     *    name="meal_list_date",
     *    requirements={"date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Meal::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the meal (YYYY-MM-DD | YYYY-MM)",
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
     * @SWG\Tag(name="Meal")
     */
    public function listByDate(Request $request, PaginatorInterface $paginator, $date)
    {
        //$this->denyAccessUnlessGranted('mealList');

        $meals = $paginator->paginate(
            $this->mealService->findAllByDate($date),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $mealsArray = array();
        foreach ($meals->getItems() as $meal) {
            $mealsArray[] = $this->mealService->toArray($meal);
        };

        return new JsonResponse($mealsArray);
    }

//TOTAL BY DATE
    /**
     * Calculates the totals for meals for a specific date
     *
     * @Route("/meal/total/{date}",
     *    name="meal_total_date",
     *    requirements={"date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *              @SWG\Property(property="meals", type="integer"),
     *              @SWG\Property(property="child", type="integer"),
     *              @SWG\Property(property="person", type="integer"),
     *              @SWG\Property(property="freeName", type="integer"),
     *              @SWG\Property(property="food", type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(property="child", type="integer"),
     *                      @SWG\Property(property="person", type="integer"),
     *                      @SWG\Property(property="freeName", type="integer"))
     *             )
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the meal (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Tag(name="Meal")
     */
    public function totalByDate($date)
    {
        //$this->denyAccessUnlessGranted('mealList');

        $meals = $this->mealService->totalMealByDate($date);

        return new JsonResponse($meals);
    }

//DISPLAY
    /**
     * Displays the meal using its id
     *
     * @Route("/meal/display/{mealId}",
     *    name="meal_list_id",
     *    requirements={"date": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("meal", expr="repository.findOneById(mealId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Meal::class))
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
     *     name="mealId",
     *     in="path",
     *     description="Id for the meal",
     *     type="string",
     * )
     * @SWG\Tag(name="Meal")
     */
    public function display(Meal $meal)
    {
        //$this->denyAccessUnlessGranted('mealDisplay', $meal);

        $mealArray = $this->mealService->toArray($meal);

        return new JsonResponse($mealArray);
    }

//DISPLAY LAST MEAL BY CHILD_ID
    /**
     * Displays the lastest meal by child_id
     *
     * @Route("/meal/latest/{childId}",
     *    name="meal_latest_by_child",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Meal::class))
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
     *     name="mealId",
     *     in="path",
     *     description="childId and  date",
     *     type="string",
     * )
     * @SWG\Tag(name="Meal")
     */
    public function latestMealByChild($childId)
    {


        $this->denyAccessUnlessGranted('mealCreate');
        if(!$meal = $this->mealService->latestMealDate($childId)) return new JsonResponse(['message' => 'Aucun repas trouvé']);       
        $date = $meal->getDate()->format('Y-m-d');
        return $this->mealByChildAndDate($childId, $date);

    }



//DISPLAY BY CHILD_ID & DATE
    /**
     * Displays the meal by child_id and date
     *
     * @Route("/meal/childDate/{childId}/{date}",
     *    name="meal_child_date",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Meal::class))
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
     *     name="mealId",
     *     in="path",
     *     description="childId and  date",
     *     type="string",
     * )
     * @SWG\Tag(name="Meal")
     */
    public function mealByChildAndDate($childId, $date)
    {
        $this->denyAccessUnlessGranted('mealCreate');
        $meal = $this->mealService->findByChildAndDate($childId, $date);
        if($meal) {
            $mealArray = $this->mealService->toArray($meal);
        } else {
            $mealArray = ['message' => 'Aucun repas trouvé'];
        }


        return new JsonResponse([$mealArray]);
    }





//CREATE
    /**
     * Creates a Meal
     *
     * @Route("/meal/create",
     *    name="meal_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="meal", ref=@Model(type=Meal::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Meal",
     *     required=true,
     *     @Model(type=MealType::class)
     * )
     * @SWG\Tag(name="Meal")
     */
    public function create(Request $request)
    {
        //$this->denyAccessUnlessGranted('mealCreate');

        $createdData = $this->mealService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies meal
     *
     * @Route("/meal/modify/{mealId}",
     *    name="meal_modify",
     *    requirements={"mealId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("meal", expr="repository.findOneById(mealId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="meal", ref=@Model(type=Meal::class)),
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
     *     name="mealId",
     *     in="path",
     *     required=true,
     *     description="Id of the meal",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Meal",
     *     required=true,
     *     @Model(type=MealType::class)
     * )
     * @SWG\Tag(name="Meal")
     */
    public function modify(Request $request, Meal $meal)
    {
        $this->denyAccessUnlessGranted('mealModify', $meal);

        $modifiedData = $this->mealService->modify($meal, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes meal and moves all the pickups as "Non pris en charge"
     *
     * @Route("/meal/delete/{mealId}",
     *    name="meal_delete",
     *    requirements={"mealId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("meal", expr="repository.findOneById(mealId)")
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
     *     name="mealId",
     *     in="path",
     *     required=true,
     *     description="Id of the meal",
     *     type="integer",
     * )
     * @SWG\Tag(name="Meal")
     */
    public function delete(Meal $meal)
    {
        $this->denyAccessUnlessGranted('mealDelete', $meal);

        $suppressedData = $this->mealService->delete($meal);

        return new JsonResponse($suppressedData);
    }
}
