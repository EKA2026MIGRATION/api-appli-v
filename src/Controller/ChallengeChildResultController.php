<?php

namespace App\Controller;

use App\Service\ChallengeChildResultService;
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
class ChallengeChildResultController extends AbstractController
{
    private $challengeChildResultService;

    public function __construct(ChallengeChildResultService $challengeChildResultService)
    {
        $this->challengeChildResultService = $challengeChildResultService;
    }

    /**
     * @Route("/challenge/calcul/stats/all/{season_id}/{sport}", name="challenge-calcul-stats-all", methods={"HEAD", "GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function calculAllChildStats($season_id, $sport)
    {
        $results = $this->challengeChildResultService->calculAllChildStats($season_id, $sport);
        return new JsonResponse($results);
    }

    /**
     * @Route("/challenge/results/all/{season_id}", name="challenge-all-result", methods={"HEAD", "GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllChildStats($season_id)
    {
        $results = $this->challengeChildResultService->getAllChildStats($season_id);
        return new JsonResponse($results);
    }

    /**
     * @Route("/challenge/bonus/update", name="challenge-bonus-update", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateBonus(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['child_id']) || !isset($data['season_id']) || !isset($data['points'])) {
            return new JsonResponse(['error' => 'Missing required fields: child_id, season_id, points'], 400);
        }

        $result = $this->challengeChildResultService->updateBonus(
            $data['child_id'],
            $data['season_id'],
            $data['points'],
            $data['name'] ?? 'Bonus manuel'
        );

        return new JsonResponse($result);
    }

}
