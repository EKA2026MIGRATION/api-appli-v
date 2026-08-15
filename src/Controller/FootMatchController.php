<?php

namespace App\Controller;

use App\Service\FootMatchService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * FoodMatchController class
 * @author Sandy Razafitrimo
 */
class FootMatchController extends AbstractController
{
    private $footMatchService;

    public function __construct(FootMatchService $footMatchService)
    {
        $this->footMatchService = $footMatchService;
    }

    /**
     * Add manually data
     *
     * @Route("/foot-match/init",
     *    name="foot_match_init",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="FootMatch")
     */
    public function init(Request $request)
    {

        $results = $this->footMatchService->init();

        return new JsonResponse($results);
    }

    /**
     * @Route("/foot-match/list", name="foot_match_list", methods={"HEAD", "GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {

        $results = $this->footMatchService->list();

        return new JsonResponse($results);
    }

    /**
     * @Route("/foot-match/create", name="foot_match_create", methods={"HEAD", "POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {

        $results = $this->footMatchService->create($request->getContent());

        return new JsonResponse($results);
    }


  /**
     * @Route("/foot-match/updateResult", name="foot_match_update_result", methods={"HEAD", "POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateResult(Request $request): JsonResponse
    {

        $results = $this->footMatchService->updateResult($request->getContent());

        return new JsonResponse($results);
    }

    /**
     * @Route("/foot-match/update", name="foot_match_update", methods={"HEAD", "POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request) {

        $results = $this->footMatchService->update($request->getContent());

        return new JsonResponse($results);
    }

}
