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
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * PickupActivity
 *
 * @ORM\Table(name="pickup_activity")
 * @ORM\Entity(repositoryClass="App\Repository\PickupActivityRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupActivity
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="pickup_activity_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pickupActivityId;

    /**
     * @var Registration
     *
     * @ORM\OneToOne(targetEntity="Registration")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="registration_id")
     */
    private $registration;

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
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32)
     */
    private $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="status_change", type="datetime")
     */
    private $statusChange;

    /**
     * @var string
     *
     * @ORM\Column(name="validated", type="string", length=16)
     */
    private $validated;

    /**
     * @var Child
     *
     * @ORM\OneToOne(targetEntity="Child")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     */
    private $child;

    /**
     * @var Sport
     *
     * @ORM\OneToOne(targetEntity="Sport")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     */
    private $sport;

    /**
     * @var Location
     *
     * @ORM\OneToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="location_id")
     */
    private $location;

    /**
     * @ORM\OneToMany(targetEntity="PickupActivityGroupActivityLink", mappedBy="pickupActivity")
     * @SWG\Property(ref=@Model(type=GroupActivity::class))
     */
    private $groupActivities;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="last_day_of_week", type="date")
     */
    private $lastDayOfWeek;

    public function __construct()
    {
        $this->groupActivities = new ArrayCollection();
    }

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
        if (null !== $objectArray['statusChange']) {
            $objectArray['statusChange'] = $objectArray['statusChange']->format('Y-m-d H:i:s');
        }

        if (null !== $objectArray['sport']) {
            $objectArray['sport'] = $this->getSport()->toArray();
        }

        if (null !== $objectArray['child']) {
            $objectArray['child'] = $this->getChild()->toArray();
        }

        if (null !== $objectArray['lastDayOfWeek']) {
            $objectArray['lastDayOfWeek'] = $objectArray['lastDayOfWeek']->format('Y-m-d');
        }


        return $objectArray;
    }

    public function getPickupActivityId(): ?int
    {
        return $this->pickupActivityId;
    }

    public function getRegistration(): ?Registration
    {
        return $this->registration;
    }

    public function setRegistration(?Registration $registration): self
    {
        $this->registration = $registration;

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

    public function getStatus(): ?string
    {
        return null !== $this->status ? strtolower($this->status) : null;
    }

    public function setStatus(?string $status): self
    {
        $this->status = !empty($status) && 'null' !== $status ? strtolower($status) : null;

        return $this;
    }

    public function getStatusChange(): ?DateTimeInterface
    {
        return $this->statusChange;
    }

    public function setStatusChange(?DateTimeInterface $statusChange): self
    {
        $this->statusChange = $statusChange;

        return $this;
    }

    public function getValidated(): ?string
    {
        return null !== $this->validated ? strtolower($this->validated) : null;
    }

    public function setValidated(?string $validated): self
    {
        $this->validated = !empty($validated) && 'null' !== $validated ? strtolower($validated) : null;

        return $this;
    }

    public function getChild(): ?Child
    {
        return $this->child;
    }

    public function setChild(?Child $child): self
    {
        $this->child = $child;

        return $this;
    }

    public function getSport(): ?Sport
    {
        return $this->sport;
    }

    public function setSport(?Sport $sport): self
    {
        $this->sport = $sport;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|PickupActivityGroupActivityLink[]
     */
    public function getGroupActivities(): Collection
    {
        return $this->groupActivities;
    }

    public function addGroupActivity(PickupActivityGroupActivityLink $groupActivity): self
    {
        if (!$this->groupActivities->contains($groupActivity)) {
            $this->groupActivities[] = $groupActivity;
            $groupActivity->setPickupActivity($this);
        }

        return $this;
    }

    public function removeGroupActivity(PickupActivityGroupActivityLink $groupActivity): self
    {
        if ($this->groupActivities->contains($groupActivity)) {
            $this->groupActivities->removeElement($groupActivity);
            // set the owning side to null (unless already changed)
            if ($groupActivity->getPickupActivity() === $this) {
                $groupActivity->setPickupActivity(null);
            }
        }

        return $this;
    }

    public function getLastDayOfWeek(): ?DateTimeInterface
    {
        return $this->lastDayOfWeek;
    }

    public function setLastDayOfWeek(?DateTimeInterface $date): self
    {
        $this->lastDayOfWeek = $date;

        return $this;
    }

}
