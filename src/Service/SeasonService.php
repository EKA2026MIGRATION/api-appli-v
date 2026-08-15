<?php

namespace App\Service;

use App\Entity\Season;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * SeasonService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SeasonService implements SeasonServiceInterface
{
    private $em;

    private $mainService;

    private $weekService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        WeekServiceInterface $weekService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->weekService = $weekService;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new Season();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'season-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Checks DateStart
        $dateStart = $this->mainService->checkDateStartIsMonday($object);

        //Persists data if season NOT exists with same DateStart
        $season = $this->em->getRepository('App\Entity\Season')->findOneByDateStart($dateStart);
        if (null === $season) {
            $this->mainService->persist($object);
        } else {
            $object = $season;
        }

        //Create related weeks
        $dateEnd = $object->getDateEnd();
        $interval = $dateStart->diff($dateEnd);
        $weeks = (int) ceil((int) $interval->format('%a') / 7);
        for ($i = 1; $i <= $weeks; $i++) {
            $data = array(
                'season' => $object->getSeasonId(),
                'kind' => 'ecole',
                'name' => 'École ' . $i,
                'code' => 'S' . $i,
                'dateStart' => $dateStart->format('Y-m-d'),
            );
            $data = json_encode($data);
            $this->weekService->create($data);
            $dateStart->add(new DateInterval('P7D'));
        }
        $this->em->refresh($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Saison ajoutée',
            'season' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Season $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Saison supprimée',
        );
    }

    /**
     * Returns the list of all persons in the array format
     * @return array
     */
    public function findAllByStatus($status)
    {
        return $this->em
            ->getRepository('App\Entity\Season')
            ->findAllByStatus($status)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Season $object)
    {
        if (null === $object->getName()) {
            throw new UnprocessableEntityHttpException('Missing data for Season -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Season $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'season-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Checks DateStart
        $dateStart = $this->mainService->checkDateStartIsMonday($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Saison modifiée',
            'season' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Season $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related weeks
        if (null !== $object->getWeeks()) {
            $weeks = array();
            foreach($object->getWeeks() as $week) {
                if (!$week->getSuppressed()) {
                    $weeks[] = $this->mainService->toArray($week->toArray());
                }
            }
            $objectArray['weeks'] = $weeks;
        }

        return $objectArray;
    }
}
