<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\ORM\Mapping as ORM;
use DateTime;


/**
 * Vehicle
 *
 * @ORM\Table(name="vehicle_washing")
 * @ORM\Entity(repositoryClass="App\Repository\VehicleWashingRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleWashing
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
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
     * @var string
     *
     * @ORM\Column(name="amount", type="float", nullable=false)
     */
    private $amount;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mileage", type="integer", nullable=true)
     */
    private $mileage;

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
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_action", type="date", nullable=true)
     */
    private $dateAction;

    /**
     * Converts the entity in an array
     */
    public function toArray($type = "full")
    {
        $objectArray = get_object_vars($this);
        if($objectArray['staff']) {
            $objectArray['staff'] = $this->getStaff()->toArray($type);
        }

        if($type == "exclude-vehicle") {
            unset($objectArray['vehicle']);
            $type = "light";
        }

        if(isset($objectArray['vehicle']) && $objectArray['vehicle']) {
            $objectArray['vehicle'] = $this->getVehicle()->toArray($type);
        }

        if($type == "light") {

            unset($objectArray['__initializer__']);
            unset($objectArray['__cloner__']);
            unset($objectArray['__isInitialized__']);
            unset($objectArray['createdAt']);
            unset($objectArray['createdBy']);
            unset($objectArray['updatedBy']);
            unset($objectArray['updatedAt']);
            unset($objectArray['suppressedAt']);
            unset($objectArray['suppressedBy']);
            unset($objectArray['suppressed']);
        }

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getMileage(): ?string
    {
        return $this->mileage;
    }

    public function setMileage(?string $mileage): self
    {
        $this->mileage = $mileage;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateAction(): ?DateTime
    {
        return $this->dateAction;
    }

    public function setDateAction($dateAction)
    {
        if (!$dateAction instanceof DateTime) {
            $dateAction = new DateTime($dateAction);
        }

        $this->dateAction = $dateAction;

        return $this;
    }

}
