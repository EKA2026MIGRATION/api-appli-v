<?php

namespace App\Controller;

use App\Entity\Family;
use App\Form\FamilyType;
use App\Service\FamilyServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * FamilyController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class FamilyController extends AbstractController
{
    private $familyService;

    public function __construct(FamilyServiceInterface $familyService)
    {
        $this->familyService = $familyService;
    }

//LIST
    /**
     * Lists all the families
     *
     * @Route("/family/list",
     *    name="family_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Family::class))
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
     * @SWG\Tag(name="Family")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('familyList');


        $familiesArray = $this->familyService->findBy();


/*
        $families = $paginator->paginate(
            $this->familyService->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $familiesArray = array();
        foreach ($families->getItems() as $family) {
            $familiesArray[] = $this->familyService->toArray($family);
        };
*/
        return new JsonResponse($familiesArray);
    }

//SEARCH
    /**
     * Searches for %{term}% in name_fr for Family
     *
     * @Route("/family/search/{term}",
     *    name="family_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Family::class))
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
     * @SWG\Tag(name="Child")
     */
    public function search(Request $request, PaginatorInterface $paginator, string $term)
    {
        $this->denyAccessUnlessGranted('familyList');

        $families = $paginator->paginate(
            $this->familyService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $familiesArray = array();
        foreach ($families->getItems() as $family) {
            $familiesArray[] = $this->familyService->toArray($family);
        };

        return new JsonResponse($familiesArray);
    }

//DISPLAY
    /**
     * Displays family
     *
     * @Route("/family/display/{familyId}",
     *    name="family_display",
     *    requirements={"familyId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("family", expr="repository.findOneById(familyId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Family::class)
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
     *     name="familyId",
     *     in="path",
     *     description="Id of the family",
     *     type="integer",
     * )
     * @SWG\Tag(name="Family")
     */
    public function display(Family $family)
    {
        $this->denyAccessUnlessGranted('familyDisplay', $family);

        $familyArray = $this->familyService->toArray($family);

        return new JsonResponse($familyArray);
    }

//CREATE
    /**
     * Creates family
     *
     * @Route("/family/create",
     *    name="family_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="family", ref=@Model(type=Family::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Family",
     *     required=true,
     *     @Model(type=FamilyType::class)
     * )
     * @SWG\Tag(name="Family")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('familyCreate');

        $createdData = $this->familyService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies family
     *
     * @Route("/family/modify/{familyId}",
     *    name="family_modify",
     *    requirements={"familyId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("family", expr="repository.findOneById(familyId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="family", ref=@Model(type=Family::class)),
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
     *     name="familyId",
     *     in="path",
     *     description="Id for the family",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Family",
     *     required=true,
     *     @Model(type=FamilyType::class)
     * )
     * @SWG\Tag(name="Family")
     */
    public function modify(Request $request, Family $family)
    {
        $this->denyAccessUnlessGranted('familyModify', $family);

        $modifiedData = $this->familyService->modify($family, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes family
     *
     * @Route("/family/delete/{familyId}",
     *    name="family_delete",
     *    requirements={"familyId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("family", expr="repository.findOneById(familyId)")
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
     *     name="familyId",
     *     in="path",
     *     description="Id for the family",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Family")
     */
    public function delete(Family $family)
    {
        $this->denyAccessUnlessGranted('familyDelete', $family);

        $suppressedData = $this->familyService->delete($family);

        return new JsonResponse($suppressedData);
    }
}
