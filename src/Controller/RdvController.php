<?php

namespace App\Controller;

use App\Entity\Rdv;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Service\RdvServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * RdvController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class RdvController extends AbstractController
{
    private $rdvService;

    public function __construct(RdvServiceInterface $rdvService)
    {
        $this->rdvService = $rdvService;
    }

//LIST
    /**
     * Displays list rdv with date optionnel
     *
     * @Route("/rdv/list/{date}",
     *    name="rdv_list",
     *    requirements={
     *        "date": "^(all|([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$"
     *    },
     *    defaults={"date": "all"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Rdv::class))
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
     *     name="date",
     *     in="path",
     *     description="RDV by list (all | YYYY-MM-DD | YYYY-MM | YYYY (default: all))",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Tag(name="Rdv")
     */
    public function list($date)
    {
        //$this->denyAccessUnlessGranted('staffPresenceDisplay');
        $rdvArray = array();
        $rdvs = $this->rdvService->findByDate($date);


        foreach ($rdvs as $rdv) {
            $rdvArray[] = $this->rdvService->toArray($rdv);
        };

        return new JsonResponse($rdvArray);
    }




//DISPLAY
    /**
     * Displays all RDV  using staffId and date (optional)
     *
     * @Route("/rdv/display/{date}",
     *    name="rdv_display",
     *    requirements={
     *        "date": "^(all|([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$"
     *    },
     *    defaults={"date": "all"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Rdv::class))
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
     *     name="date",
     *     in="path",
     *     description="RDV date by date (all | YYYY-MM-DD | YYYY-MM | YYYY (default: all))",
     *     type="string",
     *     default="null",
     * )
     * @SWG\Tag(name="Rdv")
     */
    public function display($date)
    {
        //$this->denyAccessUnlessGranted('staffPresenceDisplay');
        $rdvArray = array();
        $rdvs = $this->rdvService->findByDate($date);


        foreach ($rdvs as $rdv) {
            $rdvArray[] = $this->rdvService->toArray($rdv);
        };

        return new JsonResponse($rdvArray);
    }


//CREATE
    /**
     * Creates RDV
     *
     * @Route("/rdv/create",
     *    name="rdv_create",
     *    methods={"HEAD", "POST"})
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
     * @SWG\Tag(name="Rdv")
     */
    public function create(Request $request)
    {
        $data = $request->getContent();

        $result = $this->rdvService->create($data);

        return new JsonResponse($result) ;
    }



}
