<?php

namespace App\Controller;

use App\Entity\SurveyQuestion;
use App\Service\SurveyQuestionService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * SurveyQuestionController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class SurveyQuestionController extends AbstractController
{
    private $surveyQuestionService;

    public function __construct(SurveyQuestionService $surveyQuestionService)
    {
        $this->surveyQuestionService = $surveyQuestionService;
    }

//CREATE
    /**
     * Creates a surveyQuestion
     *
     * @Route("/survey/question/create",
     *    name="survey_question_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="survey_question", ref=@Model(type=SurveyQuestion::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="SurveyQuestion")
     */
    public function create(Request $request)
    {

        $surveyQuestion = $this->surveyQuestionService->create($request->getContent());

        return new JsonResponse($surveyQuestion);
    }

//MODIFY
    /**
     * Modifies SurveyQuestion
     *
     * @Route("/survey/question/modify/{questionId}",
     *    name="survey_question_modify",
     *    requirements={"questionId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("surveyQuestion", expr="repository.findOneById(questionId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="survey", ref=@Model(type=SurveyQuestion::class)),
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
     * @SWG\Tag(name="SurveyQuestion")
     */
    public function modify(Request $request, SurveyQuestion $surveyQuestion)
    {

        $modifiedData = $this->surveyQuestionService->modify($surveyQuestion, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes SurveyQuestion
     *
     * @Route("/survey/question/delete/{questionId}",
     *    name="survey_question_delete",
     *    requirements={"questionId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("surveyQuestion", expr="repository.findOneById(questionId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Tag(name="SurveyQuestion")
     */
    public function delete(SurveyQuestion $surveyQuestion)
    {

        $suppressedData = $this->surveyQuestionService->delete($surveyQuestion);

        return new JsonResponse($suppressedData);
    }
}
