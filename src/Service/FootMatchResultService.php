<?php

namespace App\Service;

use App\Entity\FootMatch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * FootMatchResultService class
 *
 * Base card point 70
 * Total card point 100
 * 30 cards point by year of difference
 *
 * Stats point to gain is estimate to max 500
 * Need 500 stats points to gain 30 cards points
 * To gain 1 card point, need 16.66 stats stats points
 *
 * In foot match
 * for 1 goal scored, 1 stats points
 * for 1 decisive pass, 1 stats points
 * for 3 shot saved, 1 stats points
 * for 5 ball recovered, 1 stats points
 * for 1 man of the match, 5 stats points
 *
 * that all default value system
 *
 *
 */
class FootMatchResultService implements FootMatchServiceInterface
{
    private $em;

    private $estimateMaxStatPoints = 500;
    private $estimateMaxCardPoints = 30;

    private $stepStatsPoints = 0;

    private $baseCardPoint = 70;



    private $mainService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;

        $this->stepStatsPoints = $this->estimateMaxStatPoints / $this->estimateMaxCardPoints;

    }
    
}
