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
 * StockCategory
 *
 * @ORM\Table(name="stock_category")
 * @ORM\Entity(repositoryClass="App\Repository\StockCategoryRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StockCategory
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="StockProduct", mappedBy="stockCategory")
     * @SWG\Property(ref=@Model(type=StockProduct::class))
     */
    private $stockProducts;

    public function __construct()
    {
        $this->stockProducts = new ArrayCollection();
    }

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getStockProducts(): Collection
    {
        return $this->stockProducts;
    }

    public function addStockProduct(StockProduct $stockProduct): self
    {
        if (!$this->stockProducts->contains(stockProducts)) {
            $this->stockProducts[] = stockProducts;
            $stockProduct->setCategory($this);
        }

        return $this;
    }

    public function removeStockProduct(StockProduct $stockProduct): self
    {
        if ($this->stockProducts->contains($stockProduct)) {
            $this->stockProducts->removeElement($stockProduct);
            // set the owning side to null (unless already changed)
            if ($stockProduct->getCategory() === $this) {
                $stockProduct->setCategory(null);
            }
        }

        return $this;
    }
}
