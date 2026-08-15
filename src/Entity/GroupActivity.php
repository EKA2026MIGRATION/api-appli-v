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
 * GroupActivity
 *
 * @ORM\Table(name="group_activity", indexes={@ORM\Index(name="group_activity_vehicle_FK", columns={"vehicle_id"}), @ORM\Index(name="group_activity_user_FK", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\GroupActivityRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class GroupActivity
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="group_activity_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $groupActivityId;

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
     * @var string
     *
     * @ORM\Column(name="age", type="string", length=8)
     */
    private $age;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    private $locked;

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
     * @var boolean
     *
     * @ORM\Column(name="lunch", type="boolean")
     */
    private $lunch;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string")
     */
    private $comment;

    /**
     * @var Location
     *
     * @ORM\OneToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="location_id")
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(name="area", type="string", nullable=true, length=32)
     */
    private $area;

    /**
     * @var Sport
     *
     * @ORM\OneToOne(targetEntity="Sport")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     */
    private $sport;

    /**
     * @ORM\OneToMany(targetEntity="PickupActivityGroupActivityLink", mappedBy="groupActivity")
     * @SWG\Property(ref=@Model(type=PickupActivity::class))
     */
    private $pickupActivities;

    /**
     * @ORM\OneToMany(targetEntity="GroupActivityStaffLink", mappedBy="groupActivity")
     * @SWG\Property(ref=@Model(type=Staff::class))
     */
    private $staff;

    /**
     * @var string[]
     */
    private $arrayOptimise;

    public function __construct()
    {
        $this->pickupActivities = new ArrayCollection();
        $this->staff = new ArrayCollection();
    }

    /**
     *
     * return array of group activity
     * @return Array|null
     */
    public function getArrayOptimise() {

          $staffs = null;
          if($this->getStaff()) {
            foreach($this->getStaff() as $staffLink) {
              $staff =  $staffLink->getStaff()->getPerson();
              $fullname = $staff->getFirstname().' '.$staff->getLastname();
              $staffs[] = ['fullname' => $fullname, 'firstname' =>$staff->getFirstname(), 'lastname' => $staff->getLastname(), 'personId' => $staff->getPersonId()];
            }
          }

          $childs = null;
          if($activities = $this->getPickupActivities()) {
            foreach($activities as $activityLink ) {
                if($activityLink->getPickupActivity()->getStatus() != "npec") {
                    $child = $activityLink->getPickupActivity()->getChild();
                    $childs[] = ['id' => $child->getChildId(), 'firstname' => $child->getFirstname(), 'lastname' => $child->getLastname(), 'photo' => $child->getPhoto()];
                }
            }

          } else {
            $childs = null;
          }


          $data = [
                    'id' => $this->getGroupActivityId(),
                    'name' => $this->getName(),
                    'age' => $this->getAge(),
                    'date' => $this->getDate()->format('Y-m-d'),
                    'start' => $this->getStart()->format('H:i'),
                    'end' => $this->getEnd()->format('H:i'),
                    'lunch' =>  $this->getLunch(),
                    'location' => $this->getLocation()->getName(),
                    'sport' => $this->getSport()->getName(),
                    'area' => $this->getArea(),
                    'staffs' => $staffs,
                    'childs' => $childs,
                    'comment' => $this->getComment()

          ];

          return $data;
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

        return $objectArray;
    }

    public function getGroupActivityId(): ?int
    {
        return $this->groupActivityId;
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

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(?bool $locked): self
    {
        $this->locked = $locked;

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

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

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

    public function getLunch(): ?bool
    {
        return $this->lunch;
    }

    public function setLunch(?bool $lunch): self
    {
        $this->lunch = $lunch;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(string $area): self
    {
        $this->area = $area;

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

    /**
     * @return Collection|PickupActivityGroupActivityLink[]
     */
    public function getPickupActivities(): Collection
    {
        return $this->pickupActivities;
    }

    public function addPickupActivity(PickupActivityGroupActivityLink $pickupActivity): self
    {
        if (!$this->pickupActivities->contains($pickupActivity)) {
            $this->pickupActivities[] = $pickupActivity;
            $pickupActivity->setGroupActivity($this);
        }

        return $this;
    }

    public function removePickupActivity(PickupActivityGroupActivityLink $pickupActivity): self
    {
        if ($this->pickupActivities->contains($pickupActivity)) {
            $this->pickupActivities->removeElement($pickupActivity);
            // set the owning side to null (unless already changed)
            if ($pickupActivity->getGroupActivity() === $this) {
                $pickupActivity->setGroupActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GroupActivityStaffLink[]
     */
    public function getStaff(): Collection
    {
        return $this->staff;
    }

    public function addStaff(GroupActivityStaffLink $staff, $special = true): self
    {
        if (!$this->staff->contains($staff)) {
            $this->staff[] = $staff;
            if($special) $staff->setStaff($this);
        }

        return $this;
    }

    public function removeStaff(GroupActivityStaffLink $staff): self
    {
        if ($this->staff->contains($staff)) {
            $this->staff->removeElement($staff);
            // set the owning side to null (unless already changed)
            if ($staff->getStaff() === $this) {
                $staff->setStaff(null);
            }
        }

        return $this;
    }
}
