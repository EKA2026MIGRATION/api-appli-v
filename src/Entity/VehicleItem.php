<?php

namespace App\Entity;


use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicle
 *
 * @ORM\Table(name="vehicle_item")
 * @ORM\Entity(repositoryClass="App\Repository\VehicleItemRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleItem
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="constant_key", type="string", length=20, nullable=true)
     */
    private $constantKey;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo", type="string", length=256, nullable=true)
     */
    private $photo;

    /**
     * Converts the entity in an array
     */
    public function toArray($type = "full")
    {
        $objectArray = get_object_vars($this);

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

    public function getConstantKey(): ?string
    {
        return $this->constantKey;
    }

    public function setConstantKey(?string $constantKey): self
    {
        $this->constantKey = $constantKey;

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
}
