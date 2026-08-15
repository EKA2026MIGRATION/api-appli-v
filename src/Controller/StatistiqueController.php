<?php

namespace App\Controller;

use App\Service\StatistiqueService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * StatistiqueController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StatistiqueController extends AbstractController
{
    private $statistiqueService;

    public function __construct(StatistiqueService $statistiqueService)
    {
        $this->statistiqueService = $statistiqueService;
    }


//statistique
    /**
     * display statistique ca by date
     *
     * @Route("/statistique/ca",
     *    name="statistique_ca",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Booklet::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Booklet")
     */
    public function getStatCa(Request $request)
    {

        $results = $this->statistiqueService->getStatCa($request->getContent());

        return new JsonResponse($results);
    }



//statistique
    /**
     * display statistique repartition by date
     *
     * @Route("/statistique/repartition",
     *    name="statistique_repartition",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Booklet::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Booklet")
     */
    public function getRepartition(Request $request)
    {

        $results = $this->statistiqueService->getRepartition($request->getContent());

        return new JsonResponse($results);
    }

    /**
     * calculate estimation
     * @Route("/statistique/estimation/{month}/{year}", name="statistique_estimation", methods={"HEAD", "GET"})
     * @param $month
     * @param $year
     * @return JsonResponse
     */
    public function getEstimation($month, $year): JsonResponse
    {
        $results = $this->statistiqueService->getEstimation($month, $year);

        return new JsonResponse($results);
    }

    /**
     * Update analyse strat
     * @Route("/statistique/updateStatsAnalyseStrat", name="update_analyse_strat", methods={"HEAD", "POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAnalyseStrat($elements) {
        $results = $this->statistiqueService->updateAnalyseStrat($elements);
        return new JsonResponse($results);
    }

    /**
     * List all stats analyse strat by name
     * @Route("/statistique/listStatsAnalyseStratByName", name="list_analyse_strat_by_name", methods={"HEAD", "POST"})
     * @param $name
     * @return JsonResponse
     */
    public function listsAnalyseStratByName($name)
    {
        $results = $this->statistiqueService->listAnalyseStratByName($name);
        return new JsonResponse($results);
    }

    /**
     * calculate reinscription
     * @Route("/statistique/reenrollment/{season_id}/{type}/{groupName}", name="statistique_reenrollment", methods={"HEAD", "GET"})
     * @param $season_id
     * @param $type
     * @param $groupName
     * @return JsonResponse
     */
    public function getReenrollment($season_id, $type, $groupName = null): JsonResponse
    {
        $results = $this->statistiqueService->getReenrollment($season_id, $type, $groupName);

        return new JsonResponse($results);
    }


}
