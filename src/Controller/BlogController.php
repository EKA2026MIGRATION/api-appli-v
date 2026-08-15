<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Service\BlogServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * BlogController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class BlogController extends AbstractController
{
    private $blogService;

    public function __construct(BlogServiceInterface $blogService)
    {
        $this->blogService = $blogService;
    }

//LIST
    /**
     * Lists all the blogs
     *
     * @Route("/blog/list",
     *    name="blog_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Blog::class))
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
     * @SWG\Tag(name="Blog")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('blogList');

        $blogs = $paginator->paginate(
            $this->blogService->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $blogsArray = array();
        foreach ($blogs->getItems() as $blog) {
            $blogsArray[] = $this->blogService->toArray($blog);
        };

        return new JsonResponse($blogsArray);
    }

//SEARCH
    /**
     * Searches for %{term}% in name_fr for Blog
     *
     * @Route("/blog/search/{term}",
     *    name="blog_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Blog::class))
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
        $this->denyAccessUnlessGranted('blogList');

        $blogs = $paginator->paginate(
            $this->blogService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $blogsArray = array();
        foreach ($blogs->getItems() as $blog) {
            $blogsArray[] = $this->blogService->toArray($blog);
        };

        return new JsonResponse($blogsArray);
    }

//DISPLAY
    /**
     * Displays blog
     *
     * @Route("/blog/display/{blogId}",
     *    name="blog_display",
     *    requirements={"blogId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("blog", expr="repository.findOneById(blogId)")
     *
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
     * @SWG\Parameter(
     *     name="blogId",
     *     in="path",
     *     description="Id of the blog",
     *     type="integer",
     * )
     * @SWG\Tag(name="Blog")
     */
    public function display(Blog $blog)
    {
        $this->denyAccessUnlessGranted('blogDisplay', $blog);

        $blogArray = $this->blogService->toArray($blog);

        return new JsonResponse($blogArray);
    }

//CREATE
    /**
     * Creates blog
     *
     * @Route("/blog/create",
     *    name="blog_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="blog", ref=@Model(type=Blog::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Blog",
     *     required=true,
     *     @Model(type=BlogType::class)
     * )
     * @SWG\Tag(name="Blog")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('blogCreate');

        $createdData = $this->blogService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies blog
     *
     * @Route("/blog/modify/{blogId}",
     *    name="blog_modify",
     *    requirements={"blogId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("blog", expr="repository.findOneById(blogId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="blog", ref=@Model(type=Blog::class)),
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
     *     name="blogId",
     *     in="path",
     *     description="Id for the blog",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Blog",
     *     required=true,
     *     @Model(type=BlogType::class)
     * )
     * @SWG\Tag(name="Blog")
     */
    public function modify(Request $request, Blog $blog)
    {
        $this->denyAccessUnlessGranted('blogModify', $blog);

        $modifiedData = $this->blogService->modify($blog, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes blog
     *
     * @Route("/blog/delete/{blogId}",
     *    name="blog_delete",
     *    requirements={"blogId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("blog", expr="repository.findOneById(blogId)")
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
     *     name="blogId",
     *     in="path",
     *     description="Id for the blog",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Blog")
     */
    public function delete(Blog $blog)
    {
        $this->denyAccessUnlessGranted('blogDelete', $blog);

        $suppressedData = $this->blogService->delete($blog);

        return new JsonResponse($suppressedData);
    }
}
