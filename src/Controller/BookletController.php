<?php

namespace App\Controller;

use App\Entity\Booklet;
use App\Service\BookletService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * BookletController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletController extends AbstractController
{
    private $bookletService;

    public function __construct(BookletService $bookletService)
    {
        $this->bookletService = $bookletService;
    }


//LIST
    /**
     * Lists all the Booklet
     *
     * @Route("/booklet/list",
     *    name="booklet_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Booklet::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Booklet")
     */
    public function listAll(Request $request)
    {

        $results = $this->bookletService->findAll();

        return new JsonResponse($results);
    }

//DISPLAY
    /**
     * Displays booklet
     *
     * @Route("/booklet/display/{bookletId}",
     *    name="booklet_display",
     *    requirements={"bookletId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("booklet", expr="repository.findOneById(bookletId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Booklet::class),
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Tag(name="Booklet")
     */
    public function display(Booklet $booklet)
    {

        $bookletArray = $this->bookletService->display($booklet);

        return new JsonResponse($bookletArray);
    }

//CREATE
    /**
     * Creates a booklet
     *
     * @Route("/booklet/create",
     *    name="booklet_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet", ref=@Model(type=Booklet::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Booklet")
     */
    public function create(Request $request)
    {

        $bookletArray = $this->bookletService->create($request->getContent());

        return new JsonResponse($bookletArray);
    }

//MODIFY
    /**
     * Modifies Booklet
     *
     * @Route("/booklet/modify/{bookletId}",
     *    name="booklet_modify",
     *    requirements={"bookletId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("booklet", expr="repository.findOneById(bookletId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet", ref=@Model(type=Booklet::class)),
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
     * @SWG\Tag(name="Booklet")
     */
    public function modify(Request $request, Booklet $booklet)
    {

        $modifiedData = $this->bookletService->modify($booklet, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes booklet
     *
     * @Route("/booklet/delete/{bookletId}",
     *    name="booklet_delete",
     *    requirements={"bookletId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("booklet", expr="repository.findOneById(bookletId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Tag(name="Booklet")
     */
    public function delete(Booklet $booklet)
    {

        $suppressedData = $this->bookletService->delete($booklet);

        return new JsonResponse($suppressedData);
    }
}
