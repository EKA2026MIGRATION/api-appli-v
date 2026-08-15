<?php

namespace App\Controller;

use App\Entity\SurveyChapter;
use App\Service\SurveyChapterService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * SurveyChapterController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class SurveyChapterController extends AbstractController
{
    private $surveyChapterService;

    public function __construct(SurveyChapterService $surveyChapterService)
    {
        $this->surveyChapterService = $surveyChapterService;
    }

//CREATE
    /**
     * Creates a surveyChapter
     *
     * @Route("/survey/chapter/create",
     *    name="survey_chapter_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="survey_chapter", ref=@Model(type=SurveyChapter::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="SurveyChapter")
     */
    public function create(Request $request)
    {

        $surveyChapter = $this->surveyChapterService->create($request->getContent());

        return new JsonResponse($surveyChapter);
    }

//MODIFY
    /**
     * Modifies SurveyChapter
     *
     * @Route("/survey/chapter/modify/{chapterId}",
     *    name="survey_chapter_modify",
     *    requirements={"surveyId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("surveyChapter", expr="repository.findOneById(chapterId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="survey", ref=@Model(type=SurveyChapter::class)),
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
     * @SWG\Tag(name="SurveyChapter")
     */
    public function modify(Request $request, SurveyChapter $surveyChapter)
    {

        $modifiedData = $this->surveyChapterService->modify($surveyChapter, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes survey
     *
     * @Route("/survey/chapter/delete/{surveyId}",
     *    name="survey_chapter_delete",
     *    requirements={"surveyId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("surveyChapter", expr="repository.findOneById(surveyId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Tag(name="SurveyChapter")
     */
    public function delete(SurveyChapter $surveyChapter)
    {

        $suppressedData = $this->surveyChapterService->delete($surveyChapter);

        return new JsonResponse($suppressedData);
    }
}
