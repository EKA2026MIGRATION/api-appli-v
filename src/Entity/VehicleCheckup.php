<?php

namespace App\Entity;


use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\Common\Collections\ArrayCollection;

use DateTime;


use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicle
 *
 * @ORM\Table(name="vehicle_checkup")
 * @ORM\Entity(repositoryClass="App\Repository\VehicleCheckupRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleCheckup
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="VehicleCheckupItem", mappedBy="checkup")
     */
    private $items;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_checkup", type="datetime", nullable=true)
     */
    private $dateCheckup;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="is_ok", type="integer", length=2, nullable=true)
     */
    private $isOk;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo", type="string", length=256, nullable=true)
     */
    private $photo;


    public function __construct() {
        $this->items = new ArrayCollection();
    }

    /**
     * Converts the entity in an array
     */
    public function toArray($type = "full")
    {
        $objectArray = get_object_vars($this);
        if($objectArray["staff"]) {
          if($this->getStaff()) {
            $objectArray["staff"] = $this->getStaff()->toArray("light");
          }
        }
        $objectArray["vehicle"] = $this->getVehicle()->toArray("light");

        
        if($this->getItems()) {
            $arr = [];
            foreach($this->getItemsObject() as $item)
            {
                $itemArray = $item['item']->toArray('light');
                $itemArray['isOk'] = $item['isOk'];
                $arr[] = $itemArray;
            }
            $objectArray["items"] = $arr;

        }

        if($type == "light") {
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

    public function getIsOk(): ?int
    {
        return $this->isOk;
    }

    public function setItems() {
        $this->items[] = $items;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function removeItem(VehicleCheckupItem $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }

        return $this;
    }

    public function getItemsObject()
    {
        $arr = [];
        foreach($this->items as $itemLink)
        {
            $arr[] = ['item' => $itemLink->getItem(), 'isOk' => $itemLink->getIsOk()];
        }
        return $arr;
    }


    public function setIsOk(?string $isOk): self
    {
        $this->isOk = $isOk;

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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDateCheckup(): ?DateTime
    {
        return $this->dateCheckup;
    }

    public function setDateCheckup($dateCheckup)
    {
        if (!$dateCheckup instanceof DateTime) {
            $dateCheckup = new DateTime($dateCheckup);
        }

        $this->dateCheckup = $dateCheckup;

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
}
