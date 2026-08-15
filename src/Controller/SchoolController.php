<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\SchoolType;
use App\Service\SchoolServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * SchoolController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SchoolController extends AbstractController
{
    private $schoolService;

    public function __construct(SchoolServiceInterface $schoolService)
    {
        $this->schoolService = $schoolService;
    }

//LIST
    /**
     * Lists all the schools
     *
     * @Route("/school/list",
     *    name="school_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=School::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     *     default="1",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     *     default="50",
     * )
     * @SWG\Tag(name="School")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('schoolList');

        $schools = $paginator->paginate(
            $this->schoolService->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $schoolsArray = array();
        foreach ($schools->getItems() as $school) {
            $schoolsArray[] = $this->schoolService->toArray($school);
        };

        return new JsonResponse($schoolsArray);
    }

//SEARCH
    /**
     * Searches for %{term}% in name for School
     *
     * @Route("/school/search/{term}",
     *    name="school_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=School::class))
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
     * @SWG\Parameter(
     *     name="term",
     *     in="path",
     *     required=true,
     *     description="Searched term",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     *     default="1",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     *     default="50",
     * )
     * @SWG\Tag(name="School")
     */
    public function search(Request $request, PaginatorInterface $paginator, string $term)
    {
        $this->denyAccessUnlessGranted('schoolList');

        $schools = $paginator->paginate(
            $this->schoolService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $schoolsArray = array();
        foreach ($schools->getItems() as $school) {
            $schoolsArray[] = $this->schoolService->toArray($school);
        };

        return new JsonResponse($schoolsArray);
    }

//DISPLAY
    /**
     * Displays school
     *
     * @Route("/school/display/{schoolId}",
     *    name="school_display",
     *    requirements={"schoolId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("school", expr="repository.findOneById(schoolId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=School::class)
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Parameter(
     *     name="schoolId",
     *     in="path",
     *     description="Id of the school",
     *     type="integer",
     * )
     * @SWG\Tag(name="School")
     */
    public function display(School $school)
    {
        $this->denyAccessUnlessGranted('schoolDisplay', $school);

        $schoolArray = $this->schoolService->toArray($school);

        return new JsonResponse($schoolArray);
    }

//CREATE
    /**
     * Creates school
     *
     * @Route("/school/create",
     *    name="school_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="school", ref=@Model(type=School::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the School",
     *     required=true,
     *     @Model(type=SchoolType::class)
     * )
     * @SWG\Tag(name="School")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('schoolCreate');

        $createdData = $this->schoolService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies school
     *
     * @Route("/school/modify/{schoolId}",
     *    name="school_modify",
     *    requirements={"schoolId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("school", expr="repository.findOneById(schoolId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="school", ref=@Model(type=School::class)),
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
     * @SWG\Parameter(
     *     name="schoolId",
     *     in="path",
     *     description="Id for the school",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the School",
     *     required=true,
     *     @Model(type=SchoolType::class)
     * )
     * @SWG\Tag(name="School")
     */
    public function modify(Request $request, School $school)
    {
        $this->denyAccessUnlessGranted('schoolModify', $school);

        $modifiedData = $this->schoolService->modify($school, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes school
     *
     * @Route("/school/delete/{schoolId}",
     *    name="school_delete",
     *    requirements={"schoolId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("school", expr="repository.findOneById(schoolId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
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
     * @SWG\Parameter(
     *     name="schoolId",
     *     in="path",
     *     description="Id for the school",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="School")
     */
    public function delete(School $school)
    {
        $this->denyAccessUnlessGranted('schoolDelete', $school);

        $suppressedData = $this->schoolService->delete($school);

        return new JsonResponse($suppressedData);
    }
}
