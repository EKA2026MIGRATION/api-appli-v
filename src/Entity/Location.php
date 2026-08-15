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
 * Location
 *
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Location
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="location_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $locationId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=512, nullable=false)
     */
    private $address;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo", type="string", length=256, nullable=true)
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="ProductLocationLink", mappedBy="location")
     * @SWG\Property(ref=@Model(type=Product::class))
     */
    private $products;

    /**
     * @var string
     *
     * @ORM\Column(name="name_en", type="string")
     */
    private $nameEn;

    /**
     * @var string
     *
     * @ORM\Column(name="group_front", type="string")
     */
    private $groupFront;

    /**
     * @var int
     *
     * @ORM\Column(name="front_visibility", type="integer")
     */
    private $frontVisibility;

    /**
     * @var string
     *
     * @ORM\Column(name="name_fr", type="string")
     */
    private $nameFr;

    /**
     * @var string
     *
     * @ORM\Column(name="dimension", type="string")
     */
    private $dimension;

    /**
     * @var string
     *
     * @ORM\Column(name="ages_fr", type="string")
     */
    private $agesFr;

    /**
     * @var string
     *
     * @ORM\Column(name="ages_en", type="string")
     */
    private $agesEn;


    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        return $objectArray;
    }

    public function getLocationId(): ?int
    {
        return $this->locationId;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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

    /**
     * @return Collection|ProductCategoryLink[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(ProductCategoryLink $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(ProductCategoryLink $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    /**
     * @param string $nameEn
     */
    public function setNameEn(string $nameEn)
    {
        $this->nameEn = $nameEn;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroupFront(): string
    {
        return $this->groupFront;
    }

    /**
     * @param string $groupFront
     */
    public function setGroupFront(string $groupFront): void
    {
        $this->groupFront = $groupFront;
    }

    /**
     * @return int
     */
    public function getFrontVisibility(): int
    {
        return $this->frontVisibility;
    }

    /**
     * @param int $frontVisibility
     */
    public function setFrontVisibility(int $frontVisibility)
    {
        $this->frontVisibility = $frontVisibility;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameFr(): string
    {
        return $this->nameFr;
    }

    /**
     * @param string $nameFr
     */
    public function setNameFr(string $nameFr)
    {
        $this->nameFr = $nameFr;
        return $this;

    }

    /**
     * @return string
     */
    public function getDimension(): string
    {
        return $this->dimension;
    }

    /**
     * @param string $dimension
     */
    public function setDimension(string $dimension)
    {
        $this->dimension = $dimension;
        return $this;
    }

    /**
     * @return string
     */
    public function getAgesFr(): ?string
    {
        return $this->agesFr;
    }

    /**
     * @param string $ages
     */
    public function setAgesFr(string $ages)
    {
        $this->agesFr = $ages;
        return $this;
    }


    /**
     * @return string
     */
    public function getAgesEn(): ?string
    {
        return $this->agesEn;
    }

    /**
     * @param string $ages
     */
    public function setAgesEn(string $ages)
    {
        $this->agesEn = $ages;
        return $this;
    }


}
