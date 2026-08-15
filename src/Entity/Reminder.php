<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="reminder")
 * @ORM\Entity(repositoryClass="App\Repository\ReminderRepository")
 *
 * @author Sandy Razafitrimo
 */
class Reminder
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
     * @var Vehicle
     *
     * @ORM\OneToOne(targetEntity="Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="vehicle_id")
     */
    private $vehicle;


    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_reminder", type="datetime", nullable=true)
     */
    private $dateReminder;

    /**
     * @var String|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
    

        /**
     * @var String|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var String|null
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var String|null
     *
     * @ORM\Column(name="criteria", type="string", length=255, nullable=true)
     */
    private $criteria;


    /**
     * @var String|null
     *
     * @ORM\Column(name="criteria_value", type="string", length=255, nullable=true)
     */
    private $criteriaValue;

    /**
     * @var String|null
     *
     * @ORM\Column(name="criteria_comparison", type="string", length=255, nullable=true)
     */
    private $criteriaComparison;

    /**
     * @var String|null
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

   
    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['dateReminder']) {
            $objectArray['dateReminder'] = $objectArray['dateReminder']->format('Y-m-d H:i:s');
        }
         //Specific data
         if (null !== $objectArray['vehicle']) {
            $objectArray['vehicle'] = $objectArray['vehicle']->toArray();
        }

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getDateReminder(): ?\DateTimeInterface
    {
        return $this->dateReminder;
    }

    public function setDateReminder(?\DateTimeInterface $date): self
    {
        $this->dateReminder = $date;

        return $this;
    }
  

    /**
     * Get the value of url
     *
     * @return  String|null
     */ 
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @param  String|null  $url
     *
     * @return  self
     */ 
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  String|null
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  String|null  $status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of criteria
     *
     * @return  String|null
     */ 
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Set the value of criteria
     *
     * @param  String|null  $criteria
     *
     * @return  self
     */ 
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Get the value of criteriaComparison
     *
     * @return  String|null
     */ 
    public function getCriteriaComparison()
    {
        return $this->criteriaComparison;
    }

    /**
     * Set the value of criteriaComparison
     *
     * @param  String|null  $criteriaComparison
     *
     * @return  self
     */ 
    public function setCriteriaComparison($criteriaComparison)
    {
        $this->criteriaComparison = $criteriaComparison;

        return $this;
    }

    /**
     * Get the value of criteriaValue
     *
     * @return  String|null
     */ 
    public function getCriteriaValue()
    {
        return $this->criteriaValue;
    }

    /**
     * Set the value of criteriaValue
     *
     * @param  String|null  $criteriaValue
     *
     * @return  self
     */ 
    public function setCriteriaValue($criteriaValue)
    {
        $this->criteriaValue = $criteriaValue;

        return $this;
    }

    /**
     * Get the value of vehicle
     *
     * @return  Vehicle
     */ 
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * Set the value of vehicle
     *
     * @param  Vehicle  $vehicle
     *
     * @return  self
     */ 
    public function setVehicle(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
