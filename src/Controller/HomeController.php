<?php

namespace App\Controller;

use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * HomeController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class HomeController extends AbstractController
{
//HOME
    /**
     * Home of the API
     * @return JsonResponse (true)
     *
     * @Route("/",
     *    name="home",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="boolean",
     *         default=true,
     *     )
     * )
     * @SWG\Tag(name="Root")
     */
    public function home()
    {
        return new JsonResponse('You have to be authenticated to use this API');
    }
}
