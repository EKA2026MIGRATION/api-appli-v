<?php

namespace App\Controller;

use App\Entity\BookletChild;
use App\Service\BookletChildService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BookletChildAnswer;



/**
 * BookletChildController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletChildController extends AbstractController
{
    private $bookletchildService;

    public function __construct(BookletChildService $bookletchildService)
    {
        $this->bookletchildService = $bookletchildService;
    }

// RETURN LATEST CHILD
    /**
     * RETURN LATEST CHILD
     *
     * @Route("/bookletchild/latestList/{status}",
     *    name="bookletchild_latestList",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=BookletChild::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="BookletChild")
     */
    public function latestListAll(Request $request, $status = "all")
    {

        $results = $this->bookletchildService->latestList($status);

        return new JsonResponse($results);
    }



//LIST
    /**
     * Lists all the BookletChild
     *
     * @Route("/bookletchild/list/{season}/{status}/{staff_id}",
     *    name="bookletchild_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=BookletChild::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="BookletChild")
     */
    public function listAll(Request $request, $season = "active", $status = "all", $staff_id = null)
    {

        $results = $this->bookletchildService->findAllBooklet($season, $status, $staff_id);

        return new JsonResponse($results);
    }

//DISPLAY
    /**
     * Displays bookletchild
     *
     * @Route("/bookletchild/display/{bookletchildId}",
     *    name="bookletchild_display",
     *    requirements={"bookletchildId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("bookletchild", expr="repository.findOneById(bookletchildId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=BookletChild::class),
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Tag(name="BookletChild")
     */
    public function display(BookletChild $bookletchild)
    {

        $bookletchildArray = $this->bookletchildService->display($bookletchild);

        return new JsonResponse($bookletchildArray);
    }


    //DISPLAY
    /**
     * Displays bookletchild
     *
     * @Route("/bookletchild/byChild/{childId}/{status}",
     *    name="bookletchild_by_child",
     *    requirements={"childId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=BookletChild::class),
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Tag(name="BookletChild")
     */
    public function byChild($childId, $status)
    {

        $bookletchildArray = $this->bookletchildService->findByChild($childId, $status);

        return new JsonResponse($bookletchildArray);
    }

    /**
     * @Route("bookletchild/change/evaluation/date/{dateEvaluation}",
     *     name="bookletchild_change_evaluation_date",
     *     methods={"HEAD", "GET"})
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function changeDateEvaluation(Request $request, $dateEvaluation): JsonResponse
    {
        $bookletArray = $this->bookletchildService->changeDateEvaluation($dateEvaluation);
        return new JsonResponse($bookletArray);
    }




    //DISPLAY PREVIOUS BOOKLET
    /**
     * Displays bookletchild
     *
     * @Route("/bookletchild/previousbyChild/{childId}/{bookletId}/{bookletChildId}",
     *    name="bookletchild_preivous_by_child",
     *    requirements={"childId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=BookletChild::class),
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Tag(name="BookletChild")
     */
    public function previousByChild($childId, $bookletId, $bookletChildId)
    {

        $bookletchildArray = $this->bookletchildService->previousByChild($childId, $bookletId, $bookletChildId);

        return new JsonResponse($bookletchildArray);
    }


//CREATE
    /**
     * Creates a bookletchild
     *
     * @Route("/bookletchild/create",
     *    name="bookletchild_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="bookletchild", ref=@Model(type=BookletChild::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="BookletChild")
     */
    public function create(Request $request)
    {

        $bookletchildArray = $this->bookletchildService->create($request->getContent());

        return new JsonResponse($bookletchildArray);
    }


//CREATE
    /**
     * Creates a multiple bookletchild
     *
     * @Route("/bookletchild/create/multiple",
     *    name="bookletchild_create_multiple",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="bookletchild", ref=@Model(type=BookletChild::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="BookletChild")
     */
    public function createMultiple(Request $request)
    {

        $bookletchildArray = $this->bookletchildService->createMultiple($request->getContent());

        return new JsonResponse($bookletchildArray);
    }




//MODIFY
    /**
     * Modifies BookletChild
     *
     * @Route("/bookletchild/modify/{bookletchildId}",
     *    name="bookletchild_modify",
     *    requirements={"bookletchildId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("bookletchild", expr="repository.findOneById(bookletchildId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="bookletchild", ref=@Model(type=BookletChild::class)),
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
     * @SWG\Tag(name="BookletChild")
     */
    public function modify(Request $request, BookletChild $bookletchild)
    {

        $modifiedData = $this->bookletchildService->modify($bookletchild, $request->getContent());

        return new JsonResponse($modifiedData);
    }


//UPDATE
    /**
     * Modifies bookletChildAnswer
     *
     * @Route("/bookletchild/updateAnswer/{answerId}",
     *    name="bookletchild_update_answer",
     *    requirements={"answerId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("bookletChildAnswer", expr="repository.findOneById(answerId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="bookletchild", ref=@Model(type=BookletChild::class)),
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
     * @SWG\Tag(name="BookletChild")
     */
    public function updateAnswer(Request $request, BookletChildAnswer $bookletChildAnswer)
    {

        $modifiedData = $this->bookletchildService->updateAnswer($bookletChildAnswer, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes bookletchild
     *
     * @Route("/bookletchild/delete/{bookletchildId}",
     *    name="bookletchild_delete",
     *    requirements={"bookletchildId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("bookletchild", expr="repository.findOneById(bookletchildId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Tag(name="BookletChild")
     */
    public function delete(BookletChild $bookletchild)
    {

        $suppressedData = $this->bookletchildService->delete($bookletchild);

        return new JsonResponse($suppressedData);
    }
}
