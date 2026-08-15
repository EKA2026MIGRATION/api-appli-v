<?php

namespace App\Service;

use App\Entity\ChallengeChildResult;

/**
 * ChallengeChildResultServiceInterface class
 * @author Sandy
 */
interface ChallengeChildResultServiceInterface
{

    public function toArray(ChallengeChildResult $object);
}
