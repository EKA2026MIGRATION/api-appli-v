<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicle
 *
 * @ORM\Table(name="vehicle_checkup_item")
 * @ORM\Entity(repositoryClass="App\Repository\VehicleCheckupItemRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleCheckupItem
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

  /**
    * @var VehicleCheckup
    *
    * @ORM\ManyToOne(targetEntity="VehicleCheckup", inversedBy="items")
    * @ORM\JoinColumn(name="checkup_id", referencedColumnName="id")
    */
    private $checkup;

   /**
     * @var VehicleItem
     *
     * @ORM\OneToOne(targetEntity="VehicleItem")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

    /**
     * @var string
     *
     * @ORM\Column(name="is_ok", type="integer", length=2, nullable=true)
     */
    private $isOk;


    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckup(): ?VehicleCheckup
    {
        return $this->checkup;
    }

    public function setCheckup(?VehicleCheckup $checkup): self
    {
        $this->checkup = $checkup;

        return $this;
    }

    public function getItem(): ?VehicleItem
    {
        return $this->item;
    }

    public function setItem(?VehicleItem $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getIsOk(): ?int
    {
        return $this->isOk;
    }

    public function setIsOk(?string $isOk): self
    {
        $this->isOk = $isOk;

        return $this;
    }


}
