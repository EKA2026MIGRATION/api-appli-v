<?php

namespace App\Service;

use App\Entity\ChallengeChildBonus;
use App\Entity\ChallengeChildResult;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * ChallengeChildResult class
 *
 *  Base card point 70
 *  Total card point 100
 *  30 cards point by year of difference
 *
 *  Stats point to gain is estimate to max 500
 *  Need 500 stats points to gain 30 cards points
 *  To gain 1 card point, need 16.66 stats stats points
 *
 *  In foot match
 *  for 1 goal scored, 1 stats points
 *  for 1 decisive pass, 1 stats points
 *  for 3 shot saved, 1 stats points
 *  for 2 ball recovered, 1 stats points
 *  for 1 man of the match, 5 stats points
 *
 *  that all default value system
 *
 *
 *
 *
 * @author Sandu
 */
class ChallengeChildResultService implements ChallengeChildResultServiceInterface
{
    private $em;

    private $estimateMaxStatPoints = 500;
    private $estimateMaxCardPoints = 30;

    private $stepStatsPoints = 0;

    private $baseCardPoint = 70;

    private $cardType = [
        1 => 'card_or',
        2 => 'card_ldc',
        3 => 'card_leg',
    ];

    private $points = [
        'goal' => 1, // 1 point for each goal
        'decisivePass' => 1, // 1 point for each decisive pass
        'shotsSaved' => 3,  // 1 point for every 3 shots saved
        'ballRecovered' => 2, // 1 point for every 5 balls recovered
        'manOfTheMatch' => 5,  // 5 points for man of the match
    ];


    private $mainService;


    public function __construct(
        EntityManagerInterface  $em,
        MainServiceInterface    $mainService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->stepStatsPoints = $this->estimateMaxStatPoints / $this->estimateMaxCardPoints;
    }

    /**
     * Calcul all child stats for a season
     *
     * @param $season_id
     * @param $sport
     * @return array
     */
    public function calculAllChildStats($season_id, $sport): array
    {

        if(!$season = $this->em->getRepository('App\Entity\Season')->find($season_id)) throw new NotFoundHttpException("Season not found");
        if (!in_array($sport, ['foot', 'all'])) throw new NotFoundHttpException("Sport not found");

        // get all matchs for the season
        $matchs = $this->em->getRepository('App\Entity\FootMatch')->createQueryBuilder('fm')
            ->where('fm.season = :season')
            ->andWhere('fm.score IS NOT NULL')
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult();

        // iterate all matchs to extract stats by child
        $stats = [];
        foreach($matchs as $match) {
            $results = $match->getFootMatchResults();

            foreach($results as $result) {

                $child_id = $result->getChild()->getChildId();

                // Init if not exist
                if(!isset($stats[$child_id])) {
                    $stats[$child_id] = [
                        'goal' => 0,
                        'decisivePass' => 0,
                        'shotsSaved' => 0,
                        'ballRecovered' => 0,
                        'manOfTheMatch' => 0,
                        'yellowCard' => 0,
                        'redCard' => 0,
                        'nbMatch' => 0,
                        'bonus' => 0
                    ];
                }

                // stats points cumul by child
                $stats[$child_id]['goal'] += $result->getGoal();
                $stats[$child_id]['decisivePass'] += $result->getDecisivePass();
                $stats[$child_id]['shotsSaved'] += $result->getShotsSaved();
                $stats[$child_id]['ballRecovered'] += $result->getBallonsRecuperes();
                $stats[$child_id]['manOfTheMatch'] += $result->getManOfTheMatch();
                $stats[$child_id]['yellowCard'] += $result->getYellowCard();
                $stats[$child_id]['redCard'] += $result->getRedCard();
                $stats[$child_id]['nbMatch'] += 1;

            }

        }

        // get all bonus point
        $bonus_points = $this->em->getRepository('App\Entity\ChallengeChildBonus')->createQueryBuilder('fm')
            ->where('fm.season = :season')
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult();

        // add bonus points
        foreach($bonus_points as $bonus) {
            $child_id = $bonus->getChild()->getChildId();
            if(isset($stats[$child_id])) $stats[$child_id]['bonus'] += $bonus->getPoints();
        }


        // convert to card points
        foreach($stats as $child_id => $stat) {
            $statPoint = 0;

            $statPoint += $stat['goal'] * $this->points['goal']; // 1 point for each goal
            $statPoint += $stat['decisivePass'] * $this->points['decisivePass']; // 1 point for each decisive pass
            $statPoint += ($stat['shotsSaved'] / $this->points['shotsSaved']); // 1 point for every 3 shots saved
            $statPoint += ($stat['ballRecovered'] / $this->points['ballRecovered']); // 1 point for every 5 balls recovered
            $statPoint += ($stat['manOfTheMatch']) * $this->points['manOfTheMatch']; // 5 points for each Man of the Match

            $statPoint += $stat['bonus']; // add all bonus

            //update challenge child result
            $child = $this->em->getRepository('App\Entity\Child')->find($child_id);

            $new = false;
            if(!$challengeChildResult = $this->em->getRepository('App\Entity\ChallengeChildResult')->findOneBy(['season' => $season, 'child' => $child])) {
                $challengeChildResult = new ChallengeChildResult();
                $challengeChildResult->setSeason($season);
                $challengeChildResult->setChild($child);
                $new = true;
            };

            // count the nb of season in challenge by child
            // Use strictly less-than so the count is correct whether or not the current
            // season record has already been persisted (avoids off-by-one on first run).
            $allSeasons = $this->em->getRepository('App\Entity\ChallengeChildResult')
                ->createQueryBuilder('c')
                ->where('c.child = :child')
                ->andWhere('c.season < :seasonId')
                ->setParameter('child', $child)
                ->setParameter('seasonId', $season_id)
                ->getQuery()
                ->getResult();

            $nbSeason = count($allSeasons) + 1; // +1 for the current season
            if($nbSeason >= 3) $nbSeason = 3;

            $stat['statPoint'] = $statPoint;
            $stat['cardPointValue'] = $statPoint / $this->stepStatsPoints;
            $stat['cardPoint'] = $this->baseCardPoint + $stat['cardPointValue'];
            $stat['cardType'] = $this->cardType[$nbSeason];

            // create final stats
            $stats[$child_id] = $stat;

            // update challenge child result
            $challengeChildResult->setCardPoint($stat['cardPoint']);
            $challengeChildResult->setDetails(json_encode($stat));
            $challengeChildResult->setCardType($this->cardType[$nbSeason]);

            ($new) ? $this->mainService->create($challengeChildResult) : $this->mainService->modify($challengeChildResult);
            $this->mainService->persist($challengeChildResult);
        }
        return $stats;

    }

    /**
     * Return all child stats for a season
     *
     * @param $season_id
     * @return array
     */
    public function getAllChildStats($season_id): array
    {
        if(!$season = $this->em->getRepository('App\Entity\Season')->find($season_id)) {
            throw new NotFoundHttpException("Season not found");
        }

        $challengeChildResults = $this->em->getRepository('App\Entity\ChallengeChildResult')->findBy(['season' => $season]);

        // Récupérer tous les bonus pour cette saison
        $allBonuses = $this->em->getRepository('App\Entity\ChallengeChildBonus')->findBy(['season' => $season]);
        $bonusByChild = [];
        foreach($allBonuses as $bonus) {
            $bonusByChild[$bonus->getChild()->getChildId()] = floatval($bonus->getPoints());
        }

        $stats = [];

        foreach($challengeChildResults as $challengeChildResult) {
            $details = json_decode($challengeChildResult->getDetails(), true);
            $childId = $challengeChildResult->getChild()->getChildId();

            // Mettre à jour le bonus avec la valeur en temps réel depuis la table challenge_child_bonus
            if (isset($bonusByChild[$childId])) {
                $details['bonus'] = $bonusByChild[$childId];
            } elseif (!isset($details['bonus'])) {
                $details['bonus'] = 0;
            }

            $stats[] = [
                'child_id' => $childId,
                'child_name' => $challengeChildResult->getChild()->getFirstname() . ' ' . $challengeChildResult->getChild()->getLastname(),
                'child_photo' => $challengeChildResult->getChild()->getPhoto(),
                'child_firstname' => $challengeChildResult->getChild()->getFirstname(),
                'card_point' => $challengeChildResult->getCardPoint(),
                'details' => $details,
                'card_type' => $challengeChildResult->getCardType(),
            ];
        }

        return $stats;
    }


    /**
     * Update or create a bonus for a child in a season
     *
     * @param int $child_id
     * @param int $season_id
     * @param float $points
     * @param string $name
     * @return array
     */
    public function updateBonus(int $child_id, int $season_id, float $points, string $name = 'Bonus manuel'): array
    {
        // Vérifier que l'enfant et la saison existent
        $child = $this->em->getRepository('App\Entity\Child')->find($child_id);
        if (!$child) {
            throw new NotFoundHttpException("Child not found");
        }

        $season = $this->em->getRepository('App\Entity\Season')->find($season_id);
        if (!$season) {
            throw new NotFoundHttpException("Season not found");
        }

        // Chercher si un bonus existe déjà pour cet enfant et cette saison
        $bonus = $this->em->getRepository('App\Entity\ChallengeChildBonus')->findOneBy([
            'child' => $child,
            'season' => $season
        ]);

        // Si pas de bonus existant, en créer un nouveau
        if (!$bonus) {
            $bonus = new ChallengeChildBonus();
            $bonus->setChild($child);
            $bonus->setSeason($season);
            $this->mainService->create($bonus);
        }

        // Mettre à jour les valeurs
        $bonus->setPoints($points);
        $bonus->setName($name);

        // Persister
        $this->mainService->modify($bonus);
        $this->mainService->persist($bonus);

        return [
            'success' => true,
            'bonus_id' => $bonus->getId(),
            'child_id' => $child_id,
            'season_id' => $season_id,
            'points' => $points,
            'name' => $name
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(ChallengeChildResult $object): array
    {
        return $object->toArray();
    }

}