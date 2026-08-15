<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ride
 *
 * @ORM\Table(name="ride", indexes={@ORM\Index(name="ride_vehicle_FK", columns={"vehicle_id"}), @ORM\Index(name="ride_user_FK", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\RideRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Ride
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="ride_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $rideId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    private $locked;

    /**
     * @var string|null
     *
     * @ORM\Column(name="kind", type="string", length=8, nullable=true)
     */
    private $kind;

    /**
     * @var Ride
     *
     * @ORM\OneToOne(targetEntity="Ride")
     * @ORM\JoinColumn(name="linked_ride_id", referencedColumnName="ride_id")
     */
    private $linkedRide;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="places", type="integer", nullable=true)
     */
    private $places;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="start", type="time")
     */
    private $start;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="arrival", type="time")
     */
    private $arrival;

    /**
     * @var string
     *
     * @ORM\Column(name="start_point", type="string", length=256)
     */
    private $startPoint;

    /**
     * @var string
     *
     * @ORM\Column(name="end_point", type="string", length=256)
     */
    private $endPoint;

    /**
     * @var Staff
     *
     * @ORM\OneToOne(targetEntity="Staff")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id")
     */
    private $staff;

    /**
     * @var Vehicle
     *
     * @ORM\OneToOne(targetEntity="Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="vehicle_id")
     */
    private $vehicle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="report", type="string", nullable=true)
     */
    private $report;

    /**
     * @ORM\OneToMany(targetEntity="Pickup", mappedBy="ride")
     */
    private $pickups;

    public function __construct()
    {
        $this->pickups = new ArrayCollection();
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        if($objectArray['startPoint']) {
            if (preg_match("#<b>Notice</b>#", $objectArray['startPoint'])) {
                $objectArray['startPoint'] = "<b style='color: red'>PB sur l'adresse de départ</b>" ;
            }
        }

        if($objectArray['endPoint']) {
            if (preg_match("#<b>Notice</b>#", $objectArray['endPoint'])) {
                $objectArray['endPoint'] = "<b style='color: red'>PB sur l'adresse d'arrivée</b>" ;
            }
        }

        if($objectArray['report']) {
            $objectArray['report'] = unserialize($objectArray['report']);
        }

        //Specific data
        if (null !== $objectArray['date']) {
            $objectArray['date'] = $objectArray['date']->format('Y-m-d');
        }
        if (null !== $objectArray['start']) {
            $objectArray['start'] = $objectArray['start']->format('H:i:s');
        }
        if (null !== $objectArray['arrival']) {
            $objectArray['arrival'] = $objectArray['arrival']->format('H:i:s');
        }

        return $objectArray;
    }

    public function getRideId(): ?int
    {
        return $this->rideId;
    }

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(?bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    public function getKind(): ?string
    {
        return null !== $this->kind ? strtolower($this->kind) : null;
    }

    public function setKind(?string $kind): self
    {
        $this->kind = !empty($kind) && 'null' !== $kind ? strtolower($kind) : null;

        return $this;
    }

    public function getLinkedRide(): ?Ride
    {
        return $this->linkedRide;
    }

    public function setLinkedRide(?Ride $linkedRide): self
    {
        $this->linkedRide = $linkedRide;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(?int $places): self
    {
        $this->places = $places;

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

    public function getArrival(): ?DateTimeInterface
    {
        return $this->arrival;
    }

    public function setArrival(?DateTimeInterface $arrival): self
    {
        $this->arrival = $arrival;

        return $this;
    }

    public function getStartPoint(): ?string
    {
        return $this->startPoint;
    }

    public function setStartPoint(?string $startPoint): self
    {
        $this->startPoint = $startPoint;

        return $this;
    }

    public function getEndPoint(): ?string
    {
        return $this->endPoint;
    }

    public function setEndPoint(?string $endPoint): self
    {
        $this->endPoint = $endPoint;

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

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * Return ths number of occupied places by Pickups
     * @return int
     */
    public function getOccupiedPlaces()
    {
        $occupiedPlaces = 0;
        foreach ($this->getPickups() as $pickup) {
            $occupiedPlaces += 0 === (int) $pickup->getPlaces() ? 1 : (int) $pickup->getPlaces();
        }

        return $occupiedPlaces;
    }

    /**
     * @return Collection|Pickup[]
     */
    public function getPickups(): Collection
    {
        return $this->pickups;
    }

    public function addPickup(Pickup $pickup): self
    {
        if (!$this->pickups->contains($pickup)) {
            $this->pickups[] = $pickup;
            $pickup->setRide($this);
        }

        return $this;
    }


    public function getReport()
    {
        return unserialize($this->report);
    }

    /**
     * 
     */
    public function setReport($report): self
    {

        if(is_array($report)) {
            $reportData = serialize($report);
        } else {
            $reportData = $report;
        }
        $this->report = $reportData;
        return $this;
    }


    public function removePickup(Pickup $pickup): self
    {
        if ($this->pickups->contains($pickup)) {
            $this->pickups->removeElement($pickup);
            // set the owning side to null (unless already changed)
            if ($pickup->getRide() === $this) {
                $pickup->setRide(null);
            }
        }

        return $this;
    }
}
