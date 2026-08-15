<?php

namespace App\Controller;

use App\Entity\CallTwilio;
use App\Service\CallTwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CallTwiliio class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class CallTwilioController extends AbstractController
{
    private $callTwilioService;
    private $em;

    public function __construct(EntityManagerInterface $em, CallTwilioService $callTwilioService)
    {
        $this->em = $em;
        $this->callTwilioService = $callTwilioService;
    }

    /**
     * Retrieve all calls
     * @Route("/calltwilio/retrieve/all/{from}/{to}",
     *     name="calltwilio_retrieve_all",
     *     methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *     @SWG\Property(property="status", type="boolean"),
     *     @SWG\Property(property="message", type="string"),
     *     @SWG\Property(property="calls", type="array", @SWG\Items(ref=@Model(type=CallTwilio::class))),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="CallTwilio")
     *
     */
    public function retrieveAllCalls($from = null, $to = null) {
        $calls = $this->callTwilioService->retrieveAllCalls($from, $to);
        return new JsonResponse($calls);
    }

    /**
     * Create callTwilio
     * @Route("/calltwilio/create",
     *     name="calltwilio_create",
     *     methods={"HEAD", "POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *     @SWG\Property(property="status", type="boolean"),
     *     @SWG\Property(property="message", type="string"),
     *     @SWG\Property(property="callTwilio", ref=@Model(type=CallTwilio::class)),
     *     )
     * )
     *
     *  @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *  )
     *  @SWG\Parameter(
     *      name="data",
     *      in="body",
     *      description="Data for the CallTwilio",
     *      required=true,
     *      @Model(type=CallTwilio::class)
     *  )
     *  @SWG\Tag(name="CallTwilio")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $callTwilio = $this->callTwilioService->create($request->getContent());

        return new JsonResponse($callTwilio);
    }

}
