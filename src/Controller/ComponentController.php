<?php

namespace App\Controller;

use App\Entity\Component;
use App\Form\ComponentType;
use App\Service\ComponentServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ComponentController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ComponentController extends AbstractController
{
    private $componentService;

    public function __construct(ComponentServiceInterface $componentService)
    {
        $this->componentService = $componentService;
    }

//LIST
    /**
     * Lists all the components
     *
     * @Route("/component/list",
     *    name="component_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Component::class)),
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
     * @SWG\Tag(name="Component")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('componentList');

        $components = $paginator->paginate(
            $this->componentService->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $componentsArray = array();
        foreach ($components->getItems() as $component) {
            $componentsArray[] = $this->componentService->toArray($component);
        };

        return new JsonResponse($componentsArray);
    }

//SEARCH
    /**
     * Searches for %{term}% in name_fr for Component
     *
     * @Route("/component/search/{term}",
     *    name="component_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Component::class))
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
        $this->denyAccessUnlessGranted('componentList');

        $components = $paginator->paginate(
            $this->componentService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $componentsArray = array();
        foreach ($components->getItems() as $component) {
            $componentsArray[] = $this->componentService->toArray($component);
        };

        return new JsonResponse($componentsArray);
    }

//DISPLAY
    /**
     * Displays component
     *
     * @Route("/component/display/{componentId}",
     *    name="component_display",
     *    requirements={"componentId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("component", expr="repository.findOneById(componentId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Component::class)
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
     *     name="componentId",
     *     in="path",
     *     description="Id of the component",
     *     type="integer",
     * )
     * @SWG\Tag(name="Component")
     */
    public function display(Component $component)
    {
        $this->denyAccessUnlessGranted('componentDisplay', $component);

        $componentArray = $this->componentService->toArray($component);

        return new JsonResponse($componentArray);
    }

//CREATE
    /**
     * Creates component
     *
     * @Route("/component/create",
     *    name="component_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="component", ref=@Model(type=Component::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Component",
     *     required=true,
     *     @Model(type=ComponentType::class)
     * )
     * @SWG\Tag(name="Component")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('componentCreate');

        $createdData = $this->componentService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies component
     *
     * @Route("/component/modify/{componentId}",
     *    name="component_modify",
     *    requirements={"componentId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("component", expr="repository.findOneById(componentId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="component", ref=@Model(type=Component::class)),
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
     *     name="componentId",
     *     in="path",
     *     description="Id for the component",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Component",
     *     required=true,
     *     @Model(type=ComponentType::class)
     * )
     * @SWG\Tag(name="Component")
     */
    public function modify(Request $request, Component $component)
    {
        $this->denyAccessUnlessGranted('componentModify', $component);

        $modifiedData = $this->componentService->modify($component, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes component
     *
     * @Route("/component/delete/{componentId}",
     *    name="component_delete",
     *    requirements={"componentId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("component", expr="repository.findOneById(componentId)")
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
     *     name="componentId",
     *     in="path",
     *     description="Id for the component",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Component")
     */
    public function delete(Component $component)
    {
        $this->denyAccessUnlessGranted('componentDelete', $component);

        $suppressedData = $this->componentService->delete($component);

        return new JsonResponse($suppressedData);
    }
}
