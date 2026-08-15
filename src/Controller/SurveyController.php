<?php

namespace App\Controller;

use App\Entity\Survey;
use App\Service\SurveyService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * SurveyController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class SurveyController extends AbstractController
{
    private $surveyService;

    public function __construct(SurveyService $surveyService)
    {
        $this->surveyService = $surveyService;
    }

//LIST
    /**
     * Lists all the Survey
     *
     * @Route("/survey/list",
     *    name="survey_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Survey::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Survey")
     */
    public function listAll(Request $request)
    {

        $results = $this->surveyService->findAll();

        return new JsonResponse($results);
    }

//DISPLAY
    /**
     * Displays survey
     *
     * @Route("/survey/display/{surveyId}",
     *    name="survey_display",
     *    requirements={"surveyId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("survey", expr="repository.findOneById(surveyId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Survey::class),
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Tag(name="Survey")
     */
    public function display(Survey $survey)
    {

        $surveyArray = $this->surveyService->display($survey);

        return new JsonResponse($surveyArray);
    }

//CREATE
    /**
     * Creates a survey
     *
     * @Route("/survey/create",
     *    name="survey_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="survey", ref=@Model(type=Survey::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Survey")
     */
    public function create(Request $request)
    {

        $surveyArray = $this->surveyService->create($request->getContent());

        return new JsonResponse($surveyArray);
    }

//MODIFY
    /**
     * Modifies Survey
     *
     * @Route("/survey/modify/{surveyId}",
     *    name="survey_modify",
     *    requirements={"surveyId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("survey", expr="repository.findOneById(surveyId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="survey", ref=@Model(type=Survey::class)),
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
     * @SWG\Tag(name="Survey")
     */
    public function modify(Request $request, Survey $survey)
    {

        $modifiedData = $this->surveyService->modify($survey, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes survey
     *
     * @Route("/survey/delete/{surveyId}",
     *    name="survey_delete",
     *    requirements={"surveyId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("survey", expr="repository.findOneById(surveyId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Tag(name="Survey")
     */
    public function delete(Survey $survey)
    {

        $suppressedData = $this->surveyService->delete($survey);

        return new JsonResponse($suppressedData);
    }
}
