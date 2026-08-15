<?php

namespace App\Service;

use App\Entity\SurveySession;
use App\Entity\SurveyStaffNotation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * SurveySessionService class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class SurveySessionService implements SurveySessionServiceInterface
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


    public function updateQuestions(string $data) {
        $data = json_decode($data, true);

        // update survey
        $surveySession = $this->em->getRepository('App\Entity\SurveySession')->find($data['surveySessionId']);
        $surveySession->setAnswers($data['questionId']);
        $surveySession->addStatus('answered');
        $this->mainService->modify($surveySession);
        $this->mainService->persist($surveySession);


        // coach list
        if($surveySession->getCoachList() != "") {

            $i = 0; $total = 0;
            foreach( $data['questionTypeNote']['coach'] as $note) {
                $total += $note;
                $i++;
            }

            $notation = number_format($total / $i, 2);


            foreach(explode(',',$surveySession->getCoachList()) as $coachId) {

                if(!$staff = $this->em->getRepository('App\Entity\Staff')->find($coachId)) continue;

                if(!$staffNotation = $this->em->getRepository('App\Entity\SurveyStaffNotation')->findOneBy(['staff' => $staff, 'session' => $surveySession, 'type' => 'coach'])) {
                    $staffNotation = new SurveyStaffNotation();
                }    

                $staffNotation->setStaff($staff);
                $staffNotation->setSession($surveySession);
                $staffNotation->setNotation($notation);
                $staffNotation->setNotationDetails($data['questionTypeNote']['coach']);
                $staffNotation->setType('coach');
                $this->mainService->create($staffNotation);
                $this->mainService->persist($staffNotation);
            }
            
        }

        // coach list
        if($surveySession->getDriverList() != "") {


            $i = 0; $total = 0;
            foreach( $data['questionTypeNote']['driver'] as $note) {
                $total += $note;
                $i++;
            }

            $notation = number_format($total / $i, 2);


            foreach(explode(',',$surveySession->getDriverList()) as $coachId) {

                if(!$staff = $this->em->getRepository('App\Entity\Staff')->find($coachId)) continue;

                if(!$staffNotation = $this->em->getRepository('App\Entity\SurveyStaffNotation')->findOneBy(['staff' => $staff, 'session' => $surveySession, 'type' => 'driver'])) {
                    $staffNotation = new SurveyStaffNotation();
                }    

                $staffNotation->setSurvey($surveySession->getSurvey());
                $staffNotation->setStaff($staff);
                $staffNotation->setSession($surveySession);
                $staffNotation->setNotation($notation);
                $staffNotation->setNotationDetails($data['questionTypeNote']['driver']);
                $staffNotation->setType('driver');
                $this->mainService->create($staffNotation);
                $this->mainService->persist($staffNotation);
            }
            

        }


        //Returns data
        return array(
            'status' => true,
            'message' => 'Survey session update',
            'shortUrl' => $surveySession->toArray(),
        );

    }

    public function result($surveyId) {

        $survey = $this->em->getRepository('App\Entity\Survey')->find($surveyId);

        $surveySessions = $this->em->getRepository('App\Entity\SurveySession')->findBy(['survey' => $survey]);
        $surveySessionArray = [];
        foreach($surveySessions as $surveySession) {
            $surveySessionArray[] = $surveySession->toArray();
            $surveySessionData[$surveySession->getId()] = [
                'childName' => $surveySession->getChild()->getFullname()
            ];
        }


        $staffNotations = $this->em->getRepository('App\Entity\SurveyStaffNotation')->findBy(['survey' => $survey]);

        
        foreach($staffNotations as $staffNotation) {
            $surveyNotationArray[$staffNotation->getType()][] = $staffNotation->toArray();


            if(!isset($total[$staffNotation->getType()])) {
                $total[$staffNotation->getType()] = 0;
                $nb[$staffNotation->getType()] = 0;
            }



            $total[$staffNotation->getType()] += $staffNotation->getNotation();
            $nb[$staffNotation->getType()]++;
        }

        foreach($nb as $type => $value) {
            $totalArray[$type] = [ 'average' => number_format($total[$type] / $value, 2), 'nbResult' => $value, 'sum' => number_format($total[$type], 2)];
        }


        return ['survey' => $survey->toArray(), 'staffNotations' => $surveyNotationArray, 'sessions' => $surveySessionArray, 'sessionData' => $surveySessionData, 'total' => $totalArray];
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {

        $data = json_decode($data, true);

        if(!$survey = $this->em->getRepository('App\Entity\Survey')->find($data['survey'])) return ['message' => 'no survey founded'];
        if(!$presence = $this->em->getRepository('App\Entity\ChildPresence')->find($data['presence'])) return ['message' => 'no presence founded'];
        if(!$child = $this->em->getRepository('App\Entity\Child')->find($data['child'])) return ['message' => 'no child founded'];
        if(!$registration = $this->em->getRepository('App\Entity\Registration')->find($data['registration'])) return ['message' => 'no registration founded'];
        if(!$person = $this->em->getRepository('App\Entity\Person')->find($data['person'])) return ['message' => 'no person founded'];


        $drivers = $data['drivers'];
        $coachs = $data['coachs'];

        $surveySession = new SurveySession();

        $surveySession->setSurvey($survey);
        $surveySession->setChild($child);
        $surveySession->setPerson($person);
        $surveySession->setChildPresence($presence);
        $surveySession->setRegistration($registration);
        $surveySession->setCoachList($data['coachs']);
        $surveySession->setDriverList($data['drivers']);
        $surveySession->addStatus('assigned');

        $this->mainService->create($surveySession);
        $this->mainService->persist($surveySession);


        //Returns data
        return array(
            'status' => true,
            'message' => 'Survey session created',
            'shortUrl' => $surveySession->toArray(),
        );
    }


    public function listByChildId($childId){

        if(!$child = $this->em->getRepository('App\Entity\Child')->find($childId)) return ['message' => 'no child founded'];
        $surveySessions = $this->em->getRepository('App\Entity\SurveySession')->findBy(['child' => $child]);

        $arr = [];
        foreach($surveySessions as $surveySession) {
            $arr[] = $surveySession->toArray();
        }

        return $arr;

    }

    public function display($surveySessionId) {
        if(!$surveySession = $this->em->getRepository('App\Entity\SurveySession')->find($surveySessionId)) return ['message' => 'no surveySession founded'];
        return $surveySession->toArray();
    }



    /**
     * Searches the term in the Blog collection
     * @return array
     */
    public function findAllSearch(string $term)
    {
      
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(SurveySession $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
