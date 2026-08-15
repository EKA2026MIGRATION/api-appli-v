<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use \DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Blog
 *
 * @ORM\Table(name="survey_session")
 * @ORM\Entity(repositoryClass="App\Repository\SurveySessionRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class SurveySession
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

      /**
     * @var Survey
     *
     * @ORM\OneToOne(targetEntity="Survey")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     */
    private $survey;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     */
    private $person;

    /**
     * @var Child
     *
     * @ORM\OneToOne(targetEntity="Child")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     */
    private $child;

    /**
     * @var ChildPresence
     *
     * @ORM\OneToOne(targetEntity="ChildPresence")
     * @ORM\JoinColumn(name="child_presence_id", referencedColumnName="child_presence_id")
     */
    private $childPresence;

        /**
     * @var Registration
     *
     * @ORM\OneToOne(targetEntity="Registration")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="registration_id")
     */
    private $registration;


    /**
     * @var String|null
     *
     * @ORM\Column(name="coach_list", type="string", length=255, nullable=true)
     */
    private $coachList;

    /**
     * @var String|null
     *
     * @ORM\Column(name="driver_list", type="string", length=255, nullable=true)
     */
    private $driverList;

    /**
     * @var string|null
     *
     * @ORM\Column(name="answers", type="string", nullable=true)
     */
    private $answers;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status_history", type="string", nullable=true)
     */
    private $statusHistory;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Get the value of Survey
     *
     * @return  Survey
     */ 
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Set the value of Survey
     *
     * @param  Survey  $Survey
     *
     * @return  self
     */ 
    public function setSurvey($survey)
    {
        $this->survey = $survey;

        return $this;
    }


    /**
     * Get the value of child
     * 
     * @return Child
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set the value of child
     */
    public function setChild($child): self
    {
        $this->child = $child;

        return $this;
    }
    

    /**
     * Get the value of person
     *
     * @return  Person
     */ 
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set the value of person
     *
     * @param  Person  $person
     *
     * @return  self
     */ 
    public function setPerson(?Person $person)
    {
        $this->person = $person;

        return $this;
    }


 /**
     * Get the value of childPresence
     *
     * @return  ChildPresence
     */ 
    public function getChildPresence()
    {
        return $this->childPresence;
    }

    /**
     * Set the value of childPresence
     *
     * @param  ChildPresence  $childPresence
     *
     * @return  self
     */ 
    public function setChildPresence(ChildPresence $childPresence)
    {
        $this->childPresence = $childPresence;

        return $this;
    }




 /**
     * Get the value of registration
     *
     * @return  Registration
     */ 
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Set the value of registration
     *
     * @param  Registration  $registration
     *
     * @return  self
     */ 
    public function setRegistration(?Registration $registration)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * Get the value of coachList
     *
     * @return  String|null
     */ 
    public function getCoachList()
    {
        return $this->coachList;
    }

    /**
     * Set the value of coachList
     *
     * @param  String|null  $coachList
     *
     * @return  self
     */ 
    public function setCoachList($coachList)
    {
        $this->coachList = $coachList;

        return $this;
    }

    /**
     * Get the value of driverList
     *
     * @return  String|null
     */ 
    public function getDriverList()
    {
        return $this->driverList;
    }

    /**
     * Set the value of driverList
     *
     * @param  String|null  $driverList
     *
     * @return  self
     */ 
    public function setDriverList($driverList)
    {
        $this->driverList = $driverList;

        return $this;
    }

    /**
     * Get the value of answers
     *
     * @return  string|null
     */ 
    public function getAnswers()
    {
        return null !== $this->answers ? unserialize($this->answers) : null;
    }

    /**
     * Set the value of answers
     *
     * @param  string|null  $answers
     *
     * @return  self
     */ 
    public function setAnswers($answers)
    {
       
        $this->answers = serialize($answers);

        return $this;
    }

    /**
     * Get the value of statusHistory
     *
     * @return  string|null
     */ 
    public function getStatusHistory()
    {
        return $this->statusHistory;
    }

    /**
     * Set the value of statusHistory
     *
     * @param  string|null  $statusHistory
     *
     * @return  self
     */ 
    public function setStatusHistory($statusHistory)
    {
        $this->statusHistory = $statusHistory;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  string|null
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  string|null  $status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function addStatus($status) {
        $this->setStatus($status);
        $statusHistoryArray = explode(',', $this->getStatusHistory());
        $statusHistoryArray[] = date('Y-m-d').'-'.$status;
        $newStatusHistory = implode(',', $statusHistoryArray);
        $this->setStatusHistory($newStatusHistory);
    }

    public function getLatestStatusData() {
        $status = $this->getStatusHistory();
        $statusArray = explode(',', $status);
        $latestStatus = $statusArray[count($statusArray) - 1]; 
        return $latestStatus;
    }

    public function getLatestStatus() {
        $data = $this->getLatestStatusData();
        $el = explode('-', $data);
        return $el[3];
    }

    public function getLatestStatusDate() {
        $data = $this->getLatestStatusData();
        $el = explode('-', $data);
        return $el[0].'-'.$el[1].'-'.$el[2];
    }



        /**
     * Converts the entity in an array
     */
    public function toArray($type = "light")
    {
        $objectArray = get_object_vars($this);
        if($objectArray['survey']) {
            $objectArray['survey'] = $this->getSurvey()->toArray($type);
        }

        if($objectArray['person']) {
            $objectArray['person'] = $this->getPerson()->toArray($type);
        }

        if($objectArray['answers']) {
            $objectArray['answers'] = $this->getAnswers();
        }



        if($objectArray['child']) {
            $objectArray['child'] = $this->getChild()->toArray($type);
        }

        if($objectArray['registration']) {
            $objectArray['registration'] = $this->getRegistration()->toArray($type);

            $objectArray['product'] = $this->getRegistration()->getProduct()->toArray();
        }


        if($objectArray['childPresence']) {
            $objectArray['childPresence'] = $this->getChildPresence()->toArray($type);
        }

        $objectArray['latestStatus'] = $this->getLatestStatus();
        $objectArray['latestStatusDate'] = $this->getLatestStatusDate();



        return $objectArray;
    }

}