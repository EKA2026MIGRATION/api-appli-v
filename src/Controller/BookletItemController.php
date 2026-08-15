<?php

namespace App\Controller;

use App\Entity\BookletItem;
use App\Service\BookletItemService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * BookletItemController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletItemController extends AbstractController
{
    private $bookletItemService;

    public function __construct(BookletItemService $bookletItemService)
    {
        $this->bookletItemService = $bookletItemService;
    }

//CREATE
    /**
     * Creates a bookletItem
     *
     * @Route("/booklet/item/create",
     *    name="booklet_item_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet_item", ref=@Model(type=BookletItem::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="BookletItem")
     */
    public function create(Request $request)
    {

        $bookletItem = $this->bookletItemService->create($request->getContent());

        return new JsonResponse($bookletItem);
    }

//MODIFY
    /**
     * Modifies BookletItem
     *
     * @Route("/booklet/item/modify/{itemId}",
     *    name="booklet_item_modify",
     *    requirements={"itemId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("bookletItem", expr="repository.findOneById(itemId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="booklet", ref=@Model(type=BookletItem::class)),
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
     * @SWG\Tag(name="BookletItem")
     */
    public function modify(Request $request, BookletItem $bookletItem)
    {

        $modifiedData = $this->bookletItemService->modify($bookletItem, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes BookletItem
     *
     * @Route("/booklet/item/delete/{itemId}",
     *    name="booklet_item_delete",
     *    requirements={"itemId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("bookletItem", expr="repository.findOneById(itemId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Tag(name="BookletItem")
     */
    public function delete(BookletItem $bookletItem)
    {

        $suppressedData = $this->bookletItemService->delete($bookletItem);

        return new JsonResponse($suppressedData);
    }
}
