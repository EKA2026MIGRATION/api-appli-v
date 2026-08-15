<?php

namespace App\Service;

use App\Entity\FootMatch;
use App\Entity\FootMatchResult;
use \DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * FootMatchService class
 */
class FootMatchService implements FootMatchServiceInterface
{
    private $em;

    private $mainService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
    }

    public function getPlayersData() {
        // data 2023
        /*
        $playersData = [
            [6118, 27, 43, 13, 10, 0],
            [8187, 27, 39, 13, 10, 4],
            [8386, 27, 36, 8, 11, 0],
            [12074, 27, 40, 10, 6, 0],
            [8194, 9, 1, 2, 0, 0],
            [6658, 0, 0, 0, 0, 0],
            [7724, 18, 27, 7, 6, 0],
            [8125, 27, 22, 0, 0, 0],
            [8997, 0, 0, 0, 0, 0],
            [7723, 27, 17, 0, 0, 17],
            [9876, 27, 13, 3, 0, 0],
            [11688, 27, 18, 10, 3, 0],
            [8188, 27, 37, 17, 5, 0],
            [10749, 27, 0, 1, 0, 0],
            [10703, 0, 0, 0, 0, 0],
            [8978, 0, 0, 0, 0, 0],
            [12159, 27, 5, 4, 0, 0],
            [8976, 0, 0, 0, 0, 0],
            [13055, 27, 33, 5, 9, 14],
            [12517, 27, 13, 2, 0, 0],
            [8910, 18, 4, 5, 2, 0],
            [13092, 27, 0, 1, 0, 9],
            [11809, 27, 0, 1, 0, 19],
            [11247, 27, 2, 1, 0, 3],
            [11432, 18, 6, 1, 1, 0],
            [11416, 18, 0, 0, 0, 0],
            [11392, 27, 0, 0, 1, 10],
            [8112, 18, 0, 0, 0, 0],
            [13047, 18, 3, 1, 0, 0],
            [10849, 9, 2, 1, 0, 0]
        ];*/

// 2021
        $playersData = [
    [6782, 27, 56, 15, 11, 0],
    [6118, 27, 52, 8, 7, 0],
    [8187, 27, 46, 21, 9, 0],
    [8386, 27, 51, 16, 3, 0],
    [12074, 27, 38, 13, 3, 0],
    [8194, 27, 8, 3, 1, 0],
    [6658, 20, 71, 10, 9, 0],
    [7724, 27, 15, 3, 0, 0],
    [8125, 27, 22, 15, 4, 0],
    [8997, 27, 11, 1, 1, 0],
    [7723, 27, 1, 0, 0, 0],
    [9876, 27, 4, 4, 1, 0],
    [11688, 27, 9, 5, 2, 0],
    [8188, 27, 19, 11, 2, 0],
    [10749, 27, 1, 2, 0, 0],
    [10703, 27, 7, 9, 0, 0],
    [8978, 27, 3, 2, 0, 0],
    [12159, 27, 2, 1, 0, 0],
    [8976, 18, 0, 0, 1, 0]
];


        return $playersData;
    }

    public function getVacationsDate() {
        // vances 2021 - 2022

        return [
            '2021-10-06', '2021-10-13', '2021-10-20', '2021-10-27',
            '2021-11-03', '2021-11-10', '2021-11-17', '2021-11-24',
            '2021-12-01', '2021-12-08', '2021-12-15',
            '2022-01-05', '2022-01-12', '2022-01-19', '2022-01-26',
            '2022-02-02', '2022-02-09', '2022-02-16',
            '2022-03-09', '2022-03-16', '2022-03-23', '2022-03-30',
            '2022-04-06', '2022-04-13', '2022-04-20',
            '2022-05-11', '2022-05-18', '2022-05-25'
        ];

        // vacances 2022 - 2023
        /*
        return [
            '2022-10-26',
            '2022-11-02',
            '2022-12-21', '2022-12-28',
            '2023-02-22',
            '2023-04-26', '2023-05-03'
        ];*/


    }

    public function init() {

        $playersData = $this->getPlayersData();
        $season = $this->em->getRepository('App\Entity\Season')->find(11);

        // 1. Créer tous les matchs avec des dates aléatoires
        $matchDates = [];
        $startDate = new DateTime('2021-10-01');
        $endDate = new DateTime('2022-05-31');
        $vacationDates = $this->getVacationsDate();

        while (count($matchDates) < 27) {
            $randomDate = new DateTime();
            $randomDate->setTimestamp(mt_rand($startDate->getTimestamp(), $endDate->getTimestamp()));
            // Vérifier si c'est un mercredi et pas pendant les vacances
            if ($randomDate->format('N') == 3 && !in_array($randomDate->format('Y-m-d'), $vacationDates)) {
                $matchDates[] = $randomDate->format('Y-m-d');
            }
        }

        // Insérer les matchs dans la base de données et récupérer les match_id
        foreach ($matchDates as $matchDate) {
            $footMatch = new FootMatch();
            $footMatch->setDay(new \DateTime($matchDate));
            $footMatch->setSeason($season);

            $this->mainService->create($footMatch);
            $this->mainService->persist($footMatch);
        }

        $footMatches = $this->em->getRepository('App\Entity\FootMatch')->findBy(['season' => $season]);

        foreach ($playersData as $playerData) {
            list($child_id, $matches, $total_goals, $total_decisive_passes, $total_man_of_the_match, $total_shots_saved) = $playerData;

            $child = $this->em->getRepository('App\Entity\Child')->find($child_id);

            if(!$child) continue;

            // Répartition des stats
            $goals = $this->distributeStats($total_goals, $matches);
            $decisive_passes = $this->distributeStats($total_decisive_passes, $matches);
            $man_of_the_match = $this->distributeStats($total_man_of_the_match, $matches);
            $shots_saved = $this->distributeStats($total_shots_saved, $matches);

            // Insérer les données pour chaque match
            foreach ($footMatches as $index => $footMatch) {
                if ($index >= $matches) {
                    break;
                }

                $footMatchResult = new FootMatchResult();
                $footMatchResult->setChild($child);
                $footMatchResult->setFootMatch($footMatch);
                $footMatchResult->setGoal($goals[$index]);
                $footMatchResult->setDecisivePass($decisive_passes[$index]);
                $footMatchResult->setManOfTheMatch($man_of_the_match[$index]);
                $footMatchResult->setShotsSaved($shots_saved[$index]);

                $this->mainService->create($footMatchResult);
                $this->mainService->persist($footMatchResult);
            }
        }
    }

    public function distributeStats($total, $matches) {
        $stats = array_fill(0, $matches, 0);

        for ($i = 0; $i < $total; $i++) {
            $stats[array_rand($stats)]++;
        }
        return $stats;
    }

    public function list() {
        $footMatches = $this->em->getRepository('App\Entity\FootMatch')->findBy([], ['day' => 'ASC']);
        $results = [];
        foreach ($footMatches as $footMatch) {
            $results[] = $footMatch->toArray();
        }
        return $results;
    }

    public function create($data) {
        $dataArray = json_decode($data, true);

        $match = $dataArray['newMatch'];
        $playersTeam1 = $dataArray['playersTeam1'];
        $playersTeam2 = $dataArray['playersTeam2'];
        $playersRemove = $dataArray['playersRemove'];
        
        $time = \DateTime::createFromFormat('H:i', $match['time']);

        (isset($match['id'])) ? $footMatch = $this->em->getRepository('App\Entity\FootMatch')->find($match['id']) : $footMatch = new FootMatch();

        $footMatch->setDay(new \DateTime($match['date']));
        $footMatch->setSeason($this->em->getRepository('App\Entity\Season')->find($match['season_id']));
        $footMatch->setTime($time);
        $footMatch->setLocation($match['location']);
        $footMatch->setTeam1($match['team1']);
        $footMatch->setTeam2($match['team2']);
        if(isset($match['scoreTeam1'])) $footMatch->setScore($match['scoreTeam1'] . '-' . $match['scoreTeam2']);
        if(isset($match['isWinner'])) $footMatch->setIsWinner($match['isWinner']);

        if(isset($match['score'])) $footMatch->setScore($match['score']);
        if(isset($match['is_winner'])) $footMatch->setIsWinner($match['is_winner']);

        $this->mainService->create($footMatch);
        $this->mainService->persist($footMatch);

        if(count($playersTeam1) > 0) {
            $this->updateFootMatchResultByTeam($footMatch, $playersTeam1, 1);
        }

        if(count($playersTeam2) > 0) {
            $this->updateFootMatchResultByTeam($footMatch, $playersTeam2, 2);
        }

        if(count($playersRemove) > 0) {
            foreach($playersRemove as $player) {
                if(!$child = $this->em->getRepository('App\Entity\Child')->find($player['id'])) continue;
                if(!$footMatchResult = $this->em->getRepository('App\Entity\FootMatchResult')->findOneBy(['child' => $child, 'footMatch' => $footMatch])) continue;
                $this->em->remove($footMatchResult);
                $this->em->flush();
            }
        }

        $newMatch = $this->em->getRepository('App\Entity\FootMatch')->find($footMatch->getId());

        return $newMatch->toArray();

    }

    public function updateResult($data)
    {
        $dataArray = json_decode($data, true);

        $matchId = $dataArray['match_id'];
        $childId = $dataArray['child_id'];
        $action = $dataArray['action'];
        $moment = $dataArray['moment'];
        $team   = $dataArray['team'];

        // retrieve by $child and $match
        $footMatch = $this->em->getRepository('App\Entity\FootMatch')->find($matchId);
        $child = $this->em->getRepository('App\Entity\Child')->find($childId);

        // check if $child and $match exist
        if(!$footMatch || !$child) throw new UnprocessableEntityHttpException('child or match not found');

        // retrieve footMatchResult
        $footMatchResult = $this->em->getRepository('App\Entity\FootMatchResult')->findOneBy(['child' => $child, 'footMatch' => $footMatch]);

        // check if footMatchResult exist
        if(!$footMatchResult) throw new UnprocessableEntityHttpException('footMatchResult not found');

        // transform action in setMethod and getMethod for footMatchResult. Ex: $action = "decisive_pass" become getDecisivePass and setDecisivePass
        $getMethod = 'get' . str_replace('_', '', ucwords($action, '_'));
        $setMethod = 'set' . str_replace('_', '', ucwords($action, '_'));

        // check if getMethod exist and if exist increment value in setMethod
        if(method_exists($footMatchResult, $getMethod)) {
            $value = $footMatchResult->$getMethod();
            $footMatchResult->$setMethod($value + 1);
        }

        // persist
        $this->mainService->modify($footMatchResult);
        $this->mainService->persist($footMatchResult);

        // update match details description . increment data array in field description (type text) in table foot_match
        $actionsList = [
            'result_id' => $footMatchResult->getId(),
            'child_id' => $child->getChildId(),
            'child_fullname' => $child->getFullname(),
            'action' => $action,
            'moment' => $moment,
            'team'  => $team
        ];
        $description = $footMatch->getDescription();
        $description[] = $actionsList;
        $footMatch->setDescription($description);
        $this->mainService->modify($footMatch);
        $this->mainService->persist($footMatch);


        // return footMatchResult
        return $footMatchResult->toArray();

    }

    public function updateFootMatchResultByTeam($footMatch, $players, $team) {
        foreach($players as $player) {
            if(!$child = $this->em->getRepository('App\Entity\Child')->find($player['id'])) continue;
            if(!$footMatchResult = $this->em->getRepository('App\Entity\FootMatchResult')->findOneBy(['child' => $child, 'footMatch' => $footMatch])) $footMatchResult = new FootMatchResult();
            $footMatchResult->setChild($child);
            $footMatchResult->setFootMatch($footMatch);
            $footMatchResult->setTeam($team);
            $this->mainService->create($footMatchResult);
            $this->mainService->persist($footMatchResult);
        }
    }

    public function update($data) {
        $dataArray = json_decode($data, true);

        $match_id = $dataArray['match_id'];

        if(!$footMatch = $this->em->getRepository('App\Entity\FootMatch')->find($match_id)) return ['match not found for id'.$match_id];


        if(isset($dataArray['delete'])) {

            foreach ($footMatch->getFootMatchResults() as $result) {
                $this->em->remove($result);
                $this->em->flush();
            }

            $this->em->remove($footMatch);
            $this->em->flush();

            return ['match deleted'];

        }


        if(isset($dataArray['reinit'])) {

            $footMatch->setScore(null);
            $footMatch->setIsWinner(null);
            $footMatch->setDescription(null);

            // reinit footMatchResult
            foreach ($footMatch->getFootMatchResults() as $result) {

                $result->setGoal(0);
                $result->setDecisivePass(0);
                $result->setBallonsRecuperes(0);
                $result->setManOfTheMatch(0);
                $result->setShotsSaved(0);
                $result->setYellowCard(0);
                $result->setRedCard(0);

                $this->mainService->modify($result);
                $this->mainService->persist($result);
            }
        }  else {
            // add result and endmatch
            $score = $dataArray['scoreTeam1'] . '-' . $dataArray['scoreTeam2'];
            $is_winner = $dataArray['isWinner'];

            $footMatch->setScore($score);
            $footMatch->setIsWinner($is_winner);
        }



        $this->mainService->modify($footMatch);
        $this->mainService->persist($footMatch);

        return $footMatch->toArray();
    }

}
