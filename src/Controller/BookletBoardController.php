<?php

namespace App\Controller;

use App\Entity\BookletBoard;
use App\Service\BookletBoardService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * BookletBoardController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletBoardController extends AbstractController
{
    private $bookletBoardService;

    public function __construct(BookletBoardService $bookletBoardService)
    {
        $this->bookletBoardService = $bookletBoardService;
    }

//CREATE
    /**
     * Creates a bookletBoard
     *
     * @Route("/booklet/board/create",
     *    name="booklet_board_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet_board", ref=@Model(type=BookletBoard::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="BookletBoard")
     */
    public function create(Request $request)
    {

        $bookletBoard = $this->bookletBoardService->create($request->getContent());

        return new JsonResponse($bookletBoard);
    }

//MODIFY
    /**
     * Modifies BookletBoard
     *
     * @Route("/booklet/board/modify/{boardId}",
     *    name="booklet_board_modify",
     *    requirements={"bookletId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("bookletBoard", expr="repository.findOneById(boardId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet", ref=@Model(type=BookletBoard::class)),
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
     * @SWG\Tag(name="BookletBoard")
     */
    public function modify(Request $request, BookletBoard $bookletBoard)
    {

        $modifiedData = $this->bookletBoardService->modify($bookletBoard, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes booklet
     *
     * @Route("/booklet/board/delete/{bookletId}",
     *    name="booklet_board_delete",
     *    requirements={"bookletId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("bookletBoard", expr="repository.findOneById(bookletId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Tag(name="BookletBoard")
     */
    public function delete(BookletBoard $bookletBoard)
    {

        $suppressedData = $this->bookletBoardService->delete($bookletBoard);

        return new JsonResponse($suppressedData);
    }
}
