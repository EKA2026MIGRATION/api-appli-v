<?php

namespace App\Controller;

use App\Entity\SurveySession;
use App\Service\SurveySessionServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * SurveySessionController class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class SurveySessionController extends AbstractController
{
    private $surveySessionService;

    public function __construct(SurveySessionServiceInterface $SurveySessionService)
    {
        $this->surveySessionService = $SurveySessionService;
    }

//CREATE
    /**
     * Creates SurveySession
     *
     * @Route("/surveySession/create",
     *    name="surveySession_create",
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
     * @SWG\Tag(name="SurveySession")
     */
    public function create(Request $request)
    {

        $createdData = $this->surveySessionService->create($request->getContent());

        return new JsonResponse($createdData);
    }




//CREATE
    /**
     * Creates SurveySession
     *
     * @Route("/surveySession/updateQuestions",
     *    name="surveySession_updateQuestions",
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
     * @SWG\Tag(name="SurveySession")
     */
    public function updateQuestions(Request $request)
    {

        $createdData = $this->surveySessionService->updateQuestions($request->getContent());

        return new JsonResponse($createdData);
    }

    //result survey
    /**
     * List SurveySession
     *
     * @Route("/surveySession/result/{surveyId}",
     *    name="surveySession_result",
     *    methods={"HEAD", "GET"})
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
     * @SWG\Tag(name="SurveySession")
     */
    public function resultBySurveySessionId($surveyId)
    {

        $createdData = $this->surveySessionService->result($surveyId);

        return new JsonResponse($createdData);
    }

    //List
    /**
     * List SurveySession
     *
     * @Route("/surveySession/list/{child_id}",
     *    name="surveySession_list_by_child_id",
     *    methods={"HEAD", "GET"})
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
     * @SWG\Tag(name="SurveySession")
     */
    public function listByChildId($child_id)
    {

        $createdData = $this->surveySessionService->listByChildId($child_id);

        return new JsonResponse($createdData);
    }

     //RETRIEVE
    /**
     * display SurveySession
     *
     * @Route("/surveySession/display/{surveySessionId}",
     *    name="surveySession_surveySessionId",
     *    methods={"HEAD", "GET"})
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
     * @SWG\Tag(name="SurveySession")
     */
    public function display($surveySessionId)
    {

        $createdData = $this->surveySessionService->display($surveySessionId);

        return new JsonResponse($createdData);
    }
}

