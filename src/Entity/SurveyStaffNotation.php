<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use \DateTimeInterface;
use App\Entity\SurveySession;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Blog
 *
 * @ORM\Table(name="survey_staff_notation")
 * @ORM\Entity(repositoryClass="App\Repository\SurveyStaffNotationRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class SurveyStaffNotation
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
     * @var SurveySession
     *
     * @ORM\OneToOne(targetEntity="SurveySession")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
    private $session;

    /**
     * @var Staff
     *
     * @ORM\OneToOne(targetEntity="Staff")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id")
     */
    private $staff;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=250, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notation", type="string", length=250, nullable=true)
     */
    private $notation;


    /**
     * @var string|null
     *
     * @ORM\Column(name="notation_details", type="string", length=250, nullable=true)
     */
    private $notationDetails;




    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of staff
     *
     * @return  Staff
     */ 
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     * Set the value of staff
     *
     * @param  Staff  $staff
     *
     * @return  self
     */ 
    public function setStaff(Staff $staff)
    {
        $this->staff = $staff;

        return $this;
    }

    /**
     * Get the value of survey
     *
     * @return  Survey
     */ 
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Set the value of answer
     *
     * @param  Survey  $survey
     *
     * @return  self
     */ 
    public function setSurvey(Survey $survey)
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * Get the value of SurveySession
     *
     * @return  SurveySession
     */ 
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set the value of SurveySession
     *
     * @param  SurveySession  $session
     *
     * @return  self
     */ 
    public function setSession(SurveySession $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get the value of notation
     *
     * @return  string|null
     */ 
    public function getNotation()
    {
        return $this->notation;
    }

    /**
     * Set the value of notation
     *
     * @param  string|null  $notation
     *
     * @return  self
     */ 
    public function setNotation($notation)
    {
        $this->notation = $notation;

        return $this;
    }

    /**
     * Get the value of notation
     *
     * @return  string|null
     */ 
    public function getNotationDetails()
    {
        return null !== $this->notationDetails ? unserialize($this->notationDetails) : null;
    }


       /**
     * Get the value of type
     *
     * @return  string|null
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param  string|null  $type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the value of notation
     *
     * @param  string|null  $notation
     *
     * @return  self
     */ 
    public function setNotationDetails($notationDetails)
    {
        $this->notationDetails = serialize($notationDetails);

        return $this;
    }

    /*
    * Converts the entity in an array
    */
   public function toArray($type = "light")
   {
       $objectArray = get_object_vars($this);

       $dates = array(
        'createdAt',
        'updatedAt',
        'suppressedAt',
        );
        foreach ($dates as $date) {
            if (null !== $objectArray[$date]) {
                $objectArray[$date] = $objectArray[$date]->format('Y-m-d H:i:s');
            }
        }


        if($objectArray['survey']) {
            $objectArray['surveyId'] = $this->getSurvey()->getId();
            unset($objectArray['survey']);
        }

        if($objectArray['staff']) {
            $objectArray['staffId'] = $this->getStaff()->getStaffId();
            $objectArray['staffName'] = $this->getStaff()->getFullname();
            $objectArray['person']  = $this->getStaff()->getPerson()->toArray();
            unset($objectArray['staff']);  
            unset($objectArray['person']['children']);
            unset($objectArray['person']['phones']);
            unset($objectArray['person']['createdAt']);
            unset($objectArray['person']['updatedAt']);
        }

        if($objectArray['notationDetails']) {
            $objectArray['notationDetails'] = $this->getNotationDetails();
        }


        if($objectArray['session']) {
            $objectArray['sessionId'] = $this->getSession()->getId();
            unset($objectArray['session']);

        }



       return $objectArray;
   }
}
