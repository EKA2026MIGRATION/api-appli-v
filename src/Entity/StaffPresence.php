<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * StaffPresence
 *
 * @ORM\Table(name="staff_presence")
 * @ORM\Entity(repositoryClass="App\Repository\StaffPresenceRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class StaffPresence
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="staff_presence_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $staffPresenceId;

    /**
     * @var Staff
     *
     * @ORM\OneToOne(targetEntity="Staff")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id")
     */
    private $staff;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="start", type="time")
     */
    private $start;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="end", type="time")
     */
    private $end;

    /**
     * @var String|null
     *
     * @ORM\Column(name="type_name", type="string", length=255, nullable=true)
     */
    private $typeName;

       /**
     * @var String|null
     *
     * @ORM\Column(name="teams_id_list", type="string", length=255, nullable=true)
     */
    private $teamsIdList;


    /**
     * @var Season
     *
     * @ORM\OneToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="location_id")
     */
    private $location;

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['date']) {
            $objectArray['date'] = $objectArray['date']->format('Y-m-d');
        }
        if (null !== $objectArray['start']) {
            $objectArray['start'] = $objectArray['start']->format('H:i:s');
        }
        if (null !== $objectArray['end']) {
            $objectArray['end'] = $objectArray['end']->format('H:i:s');
        }

        if (null !== $objectArray['location']) {
            $objectArray['location'] = $this->getLocation()->getName();
        }


        return $objectArray;
    }

    public function getStaffPresenceId(): ?int
    {
        return $this->staffPresenceId;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;

        return $this;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    public function setTypeName(?string $typeName): self
    {
        $this->typeName = $typeName;

        return $this;
    }


    /**
     * Get the value of teamsIdList
     *
     * @return  String|null
     */ 
    public function getTeamsIdList()
    {
        return $this->teamsIdList;
    }

    /**
     * Set the value of teamsIdList
     *
     * @param  String|null  $teamsIdList
     *
     * @return  self
     */ 
    public function setTeamsIdList($teamsIdList)
    {
        $this->teamsIdList = $teamsIdList;

        return $this;
    }

    /**
     * Get the value of location
     *
     * @return  Location
     */ 
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set the value of location
     *
     * @param  Location  $location
     *
     * @return  self
     */ 
    public function setLocation(Location $location)
    {
        $this->location = $location;

        return $this;
    }
}
