<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Staff
 *
 * @ORM\Table(name="staff", uniqueConstraints={@ORM\UniqueConstraint(name="staff_UN", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\StaffRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Staff
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="staff_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $staffId;

    /**
     * @var string
     *
     * @ORM\Column(name="kind", type="string", nullable=true)
     */
    private $kind;

    /**
     * @var int
     *
     * @ORM\Column(name="is_supervisor", type="string", nullable=true)
     */
    private $isSupervisor;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     */
    private $person;

    /**
     * @var int
     *
     * @ORM\Column(name="max_children", type="integer", nullable=true)
     */
    private $maxChildren;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=true)
     */
    private $priority;

    /**
     * @var Vehicle
     *
     * @ORM\OneToOne(targetEntity="Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="vehicle_id")
     */
    private $vehicle;

    /**
     * @var Address
     *
     * @ORM\OneToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="address_id")
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="DriverZone", mappedBy="staff", cascade={"persist"})
     * @SWG\Property(ref=@Model(type=DriverZone::class))
     */
    private $driverZones;

    /**
     * @ORM\OneToMany(targetEntity="GroupActivityStaffLink", mappedBy="staff", cascade={"persist"})
     * @SWG\Property(ref=@Model(type=GroupActivity::class))
     */
    private $groupActivities;

    /**
     * @var int
     *
     * @ORM\Column(name="is_active", type="integer")
     */
    private $isActive;



    /**
     * @ORM\OneToMany(targetEntity="Child", mappedBy="staff")
     * @SWG\Property(ref=@Model(type=Child::class))
     */
    private $childs;

    public function __construct()
    {
        $this->driverZones = new ArrayCollection();
        $this->groupActivities = new ArrayCollection();
        $this->notificationLinks = new ArrayCollection();
        $this->childs = new ArrayCollection();

        if($this->getIsActive() == null) {
            $this->setIsActive(1);
        }

    }

    /**
     * Converts the entity in an array
     */
    public function toArray($type = "full")
    {
        $objectArray = get_object_vars($this);

        //Specific data
        $objectArray['totalZones'] = $this->getDriverZones()->count();

        $objectArray['phone_number'] = $this->getPerson()->getPhones();

        if($this->getPerson()) {
          $objectArray['person'] = $this->getPerson()->toArray($type);
        }

        $objectArray['fullname'] = $this->getFullname();
        $objectArray['fullnameReverse'] = $this->getFullnameReverse();

        if($type == "light") {

            unset($objectArray['totalZones']);
            unset($objectArray['maxChildren']);
            unset($objectArray['priority']);
            unset($objectArray['vehicle']);
            unset($objectArray['address']);
            unset($objectArray['driverZones']);
            unset($objectArray['groupActivities']);
            if(isset($objectArray['__initializer__'])) unset($objectArray['__initializer__']);
           if(isset($objectArray['__cloner__'])) unset($objectArray['__cloner__']);
           if(isset($objectArray['__isInitialized__'])) unset($objectArray['__isInitialized__']);
           if(isset($objectArray['createdAt'])) unset($objectArray['createdAt']);
           if(isset($objectArray['createdBy'])) unset($objectArray['createdBy']);
           if(isset($objectArray['updatedBy'])) unset($objectArray['updatedBy']);
           if(isset($objectArray['updatedAt'])) unset($objectArray['updatedAt']);
           if(isset($objectArray['suppressedAt'])) unset($objectArray['suppressedAt']);
           if(isset($objectArray['suppressedBy'])) unset($objectArray['suppressedBy']);
           if(isset($objectArray['suppressed'])) unset($objectArray['suppressed']);
        }

        return $objectArray;
    }

    public function getStaffId(): ?int
    {
        return $this->staffId;
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


    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getFullname() {
        return $this->person->getFullname();
    }

    public function getFullnameReverse() {
        return $this->person->getFullnameReverse();
    }

    public function getIsSupervisor(): ?Int
    {
        return $this->isSupervisor;
    }

    public function setIsSupervisor(?int $isSupervisor): self
    {
        $this->isSupervisor = $isSupervisor;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getMaxChildren(): ?int
    {
        return $this->maxChildren;
    }

    public function setMaxChildren(?int $maxChildren): self
    {
        $this->maxChildren = $maxChildren;

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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection|DriverZone[]
     */
    public function getDriverZones(): Collection
    {
        return $this->driverZones;
    }

    public function addDriverZone(DriverZone $driverZone): self
    {
        if (!$this->driverZones->contains($driverZone)) {
            $this->driverZones[] = $driverZone;
            $driverZone->setStaff($this);
        }

        return $this;
    }

    public function removeDriverZone(DriverZone $driverZone): self
    {
        if ($this->driverZones->contains($driverZone)) {
            $this->driverZones->removeElement($driverZone);
            // set the owning side to null (unless already changed)
            if ($driverZone->getStaff() === $this) {
                $driverZone->setStaff(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GroupActivityStaffLink[]
     */
    public function getGroupActivities(): Collection
    {
        return $this->groupActivities;
    }

    public function addGroupActivity(GroupActivityStaffLink $groupActivity): self
    {
        if (!$this->groupActivities->contains($groupActivity)) {
            $this->groupActivities[] = $groupActivity;
            $groupActivity->setStaff($this);
        }

        return $this;
    }

    public function removeGroupActivity(GroupActivityStaffLink $groupActivity): self
    {
        if ($this->groupActivities->contains($groupActivity)) {
            $this->groupActivities->removeElement($groupActivity);
            // set the owning side to null (unless already changed)
            if ($groupActivity->getStaff() === $this) {
                $groupActivity->setStaff(null);
            }
        }

        return $this;
    }

    public function addChild($child) {
        if (!$this->childs->contains($child)) {
            $this->childs[] = $child;

            // test if staff not exist in child
            if($child->getStaff() != $this) {
                $child->addStaff($this);
            }
        }
        return $this;
    }

    public function removeChild($child) {
        if ($this->childs->contains($child)) {
            $this->childs->removeElement($child);

            // test if staff not exist in child
            if ($child->getStaff() === $this) {
                $child->setStaff(null);
            }
        }

        return $this;
    }

    public function setIsActive($is_active) {
        $this->isActive = $is_active;
        return $this;
      }
  
      public function getIsActive()
      {
          return $this->isActive;
      }
  

}
