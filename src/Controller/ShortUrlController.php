<?php

namespace App\Controller;

use App\Entity\ShortUrl;
use App\Service\ShortUrlServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ShortUrlController class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class ShortUrlController extends AbstractController
{
    private $shortUrlService;

    public function __construct(ShortUrlServiceInterface $shortUrlService)
    {
        $this->shortUrlService = $shortUrlService;
    }

//LIST
    /**
     * Lists all the blogs
     *
     * @Route("/shortUrl/list",
     *    name="short_url_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ShortUrl::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="ShortUrl")
     */
    public function listAll(Request $request)
    {

       
        $result = $this->shortUrlService->findAll();

        return new JsonResponse($result);
    }

//DISPLAY
    /**
     * Displays blog
     *
     * @Route("/shortUrl/retrieve/{shortName}",
     *    name="shortUrl_display",
     *    methods={"HEAD", "GET"})     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Blog::class)
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Tag(name="ShortUrl")
     */
    public function display($shortName)
    {

        $result = $this->shortUrlService->findByShortCode($shortName);

        return new JsonResponse($result);
    }

//CREATE
    /**
     * Creates ShortUrl
     *
     * @Route("/shortUrl/create",
     *    name="shortUrl_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="blog", ref=@Model(type=ShortUrl::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Blog")
     */
    public function create(Request $request)
    {

        $createdData = $this->shortUrlService->create($request->getContent());

        return new JsonResponse($createdData);
    }
}
