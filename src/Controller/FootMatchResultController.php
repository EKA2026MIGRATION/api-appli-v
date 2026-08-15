<?php

namespace App\Controller;

use App\Service\FootMatchResultService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * FoodMatchResultController class
 * @author Sandy Razafitrimo
 */
class FootMatchResultController extends AbstractController
{
    private $footMatchResultService;

    public function __construct(FootMatchResultService $footMatchResultService)
    {
        $this->footMatchResultService = $footMatchResultService;
    }

    /**
     * @Route("/foot-challenge/calcul/stats/all/{season_id}", name="foot-challenge-calcul-stats-all", methods={"HEAD", "GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function calculAllChildStats($season_id)
    {
        $results = $this->footMatchResultService->calculAllChildStats($season_id);
        return new JsonResponse($results);
    }

}
