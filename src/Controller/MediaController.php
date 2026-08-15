<?php

namespace App\Controller;

use App\Entity\Media;
use App\Service\MediaService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * MediaController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class MediaController extends AbstractController
{
    private $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
//LIST
    /**
     * Lists all the media
     *
     * @Route("/media/list/child/{childId}/{status}",
     *    name="media_list_child",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Media::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Media")
     */
    public function listByChild(Request $request, $childId, $status = "online")
    {

        $results = $this->mediaService->listByChild($childId, $status);

        return new JsonResponse($results);
    }

//LIST
    /**
     * Lists all the media
     *
     * @Route("/media/list/{status}",
     *    name="media_list_status",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Media::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Media")
     */
    public function listByStatus(Request $request, $status = "awaiting")
    {

        $results = $this->mediaService->list($status);

        return new JsonResponse($results);
    }

   //CREATE
    /**
     * Creates a Media
     *
     * @Route("/media/create",
     *    name="media_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Media")
     */
    public function create(Request $request)
    {

        $mediaArray = $this->mediaService->create($request->getContent());

        return new JsonResponse($mediaArray);
    }

//MODIFY
    /**
     * Modifies Booklet
     *
     * @Route("/media/modify/{mediaId}",
     *    name="media_modify",
     *    requirements={"mediaId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("media", expr="repository.findOneById(mediaId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="media", ref=@Model(type=Media::class)),
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
    public function modify(Request $request, Media $media)
    {

        $modifiedData = $this->mediaService->modify($media, $request->getContent());

        return new JsonResponse($modifiedData);
    }

    /**
     * updateAllDateLatestMedia
     * @Route("/media/updateAllDateLatestMedia",
     *     name="media_updateAllDateLatestMedia",
     *     methods={"HEAD", "GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *     )
     * )
     *
     */
    public function updateAllDateLatestMedia()
    {
        $response = $this->mediaService->updateAllDateLatestMedia();
        return new JsonResponse($response);
    }

//DELETE
    /**
     * Deletes media
     *
     * @Route("/media/delete/{mediaId}",
     *    name="media_delete",
     *    requirements={"mediaId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("media", expr="repository.findOneById(mediaId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Tag(name="Media")
     */
    public function delete(Media $media)
    {

        $suppressedData = $this->mediaService->delete($media);

        return new JsonResponse($suppressedData);
    }
}
