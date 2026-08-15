<?php

namespace App\Service;

use App\Entity\SurveyStaffNotation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * SurveySessionService class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class SurveyStaffNotationService implements SurveyStaffNotationServiceInterface
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



    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {

        
    }


    /**
     * {@inheritdoc}
     */
    public function toArray(SurveyStaffNotation $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
