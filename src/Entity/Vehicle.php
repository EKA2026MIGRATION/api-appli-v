<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicle
 *
 * @ORM\Table(name="vehicle")
 * @ORM\Entity(repositoryClass="App\Repository\VehicleRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Vehicle
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $vehicleId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="matriculation", type="string", length=16, nullable=false)
     */
    private $matriculation;

    /**
     * @var string
     *
     * @ORM\Column(name="mileage", type="string", length=16, nullable=false)
     */
    private $mileage;

    /**
     * @var string|null
     *
     * @ORM\Column(name="combustible", type="string", length=16, nullable=true)
     */
    private $combustible;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="places", type="integer", nullable=true)
     */
    private $places;

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

    public function getVehicleId(): ?int
    {
        return $this->vehicleId;
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

    public function getMileage(): ?string
    {
        return $this->mileage;
    }

    public function setMileage(?string $mileage): self
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getMatriculation(): ?string
    {
        return $this->matriculation;
    }

    public function setMatriculation(?string $matriculation): self
    {
        $this->matriculation = $matriculation;

        return $this;
    }

    public function getCombustible(): ?string
    {
        return null !== $this->combustible ? strtolower($this->combustible) : null;
    }

    public function setCombustible(?string $combustible): self
    {
        $this->combustible = !empty($combustible) && 'null' !== $combustible ? strtolower($combustible) : null;

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
