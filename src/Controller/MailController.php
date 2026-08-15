<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Form\MailType;
use App\Service\MailServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * MailController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class MailController extends AbstractController
{
    private $mailService;

    public function __construct(MailServiceInterface $mailService)
    {
        $this->mailService = $mailService;
    }

//LIST
    /**
     * Lists all the mails
     *
     * @Route("/mail/list",
     *    name="mail_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Mail::class))
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
     * @SWG\Tag(name="Mail")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('mailList');

        $mails = $paginator->paginate(
            $this->mailService->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $mailsArray = array();
        foreach ($mails->getItems() as $mail) {
            $mailsArray[] = $this->mailService->toArray($mail);
        };

        return new JsonResponse($mailsArray);
    }

//SEARCH
    /**
     * Searches for %{term}% in name_fr for Mail
     *
     * @Route("/mail/search/{term}",
     *    name="mail_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Mail::class))
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
        $this->denyAccessUnlessGranted('mailList');

        $mails = $paginator->paginate(
            $this->mailService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $mailsArray = array();
        foreach ($mails->getItems() as $mail) {
            $mailsArray[] = $this->mailService->toArray($mail);
        };

        return new JsonResponse($mailsArray);
    }

//DISPLAY
    /**
     * Displays mail
     *
     * @Route("/mail/display/{mailId}",
     *    name="mail_display",
     *    requirements={"mailId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("mail", expr="repository.findOneById(mailId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Mail::class)
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
     *     name="mailId",
     *     in="path",
     *     description="Id of the mail",
     *     type="integer",
     * )
     * @SWG\Tag(name="Mail")
     */
    public function display(Mail $mail)
    {
        $this->denyAccessUnlessGranted('mailDisplay', $mail);

        $mailArray = $this->mailService->toArray($mail);

        return new JsonResponse($mailArray);
    }

//CREATE
    /**
     * Creates mail
     *
     * @Route("/mail/create",
     *    name="mail_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="mail", ref=@Model(type=Mail::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Mail",
     *     required=true,
     *     @Model(type=MailType::class)
     * )
     * @SWG\Tag(name="Mail")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('mailCreate');

        $createdData = $this->mailService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies mail
     *
     * @Route("/mail/modify/{mailId}",
     *    name="mail_modify",
     *    requirements={"mailId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("mail", expr="repository.findOneById(mailId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="mail", ref=@Model(type=Mail::class)),
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
     *     name="mailId",
     *     in="path",
     *     description="Id for the mail",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Mail",
     *     required=true,
     *     @Model(type=MailType::class)
     * )
     * @SWG\Tag(name="Mail")
     */
    public function modify(Request $request, Mail $mail)
    {
        $this->denyAccessUnlessGranted('mailModify', $mail);

        $modifiedData = $this->mailService->modify($mail, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes mail
     *
     * @Route("/mail/delete/{mailId}",
     *    name="mail_delete",
     *    requirements={"mailId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("mail", expr="repository.findOneById(mailId)")
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
     *     name="mailId",
     *     in="path",
     *     description="Id for the mail",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Mail")
     */
    public function delete(Mail $mail)
    {
        $this->denyAccessUnlessGranted('mailDelete', $mail);

        $suppressedData = $this->mailService->delete($mail);

        return new JsonResponse($suppressedData);
    }
}
