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
 * Sport
 *
 * @ORM\Table(name="sport")
 * @ORM\Entity(repositoryClass="App\Repository\SportRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Sport
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="sport_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sportId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="kind", type="string", length=32, nullable=false)
     */
    private $kind;


    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=32, nullable=false)
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity="ProductSportLink", mappedBy="sport")
     * @SWG\Property(ref=@Model(type=Product::class))
     */
    private $products;

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

    public function getSportId(): ?int
    {
        return $this->sportId;
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

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(string $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return Sport
     */
    public function setColor(string $color)
    {
        $this->color = $color;

        return $this;
    }



    /**
     * @return Collection|ProductSportLink[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(ProductSportLink $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSport($this);
        }

        return $this;
    }

    public function removeProduct(ProductSportLink $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getSport() === $this) {
                $product->setSport(null);
            }
        }

        return $this;
    }
}
