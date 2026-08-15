<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use PhpParser\Node\Expr\Cast\Object_;
use Swagger\Annotations as SWG;

use DateTime;


/**
 * Blog
 *
 * @ORM\Table(name="stock_order")
 * @ORM\Entity(repositoryClass="App\Repository\StockOrderRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StockOrder
{
    use CreationTrait;
    use UpdateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Object_
     *
     * @ORM\ManyToOne(targetEntity="StockProduct")
     * @ORM\JoinColumn(name="stock_product_id", referencedColumnName="id")
     */
    private $stockProduct;

    /**
     * @ORM\Column(name="date_order", type="datetime", nullable=true)
     */
    private $dateOrder;

    /**
     * @var string|null
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var int|null
     *
     * @ORM\Column(name="quantity_target", type="integer", nullable=true)
     */
    private $quantityTarget;

    /**
     * @var int|null
     *
     * @ORM\Column(name="is_valid", type="integer", nullable=true)
     */
    private $isValid;

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        if($objectArray['stockProduct']) $objectArray['stockProduct'] = $this->getStockProduct()->toArray();
        $objectArray['dateOrder'] = $this->getDateOrder()->format('Y-m-d');

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Get the value of name
     *
     * @return  string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string|null  $name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Object_
     */
    public function getStockProduct(): StockProduct
    {
        return $this->stockProduct;
    }

    /**
     * @param Object_ $productRef
     */
    public function setStockProduct(StockProduct $stockProduct)
    {
        $this->stockProduct = $stockProduct;

        return $this;
    }


    public function getDateOrder(): ?\DateTimeInterface
    {
        return $this->dateOrder;
    }

    public function setDateOrder(\DateTimeInterface $dateOrder): self
    {
        $this->dateOrder = $dateOrder;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param null|string $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return null|string
     */
    public function getQuantityTarget(): ?int
    {
        return $this->quantityTarget;
    }

    /**
     * @param null|string $quantity
     */
    public function setQuantityTarget(int $quantityTarget)
    {
        $this->quantityTarget = $quantityTarget;
    }


    /**
     * @return null|string
     */
    public function getIsValid(): ?int
    {
        return $this->isValid;
    }

    /**
     * @param null|string $quantity
     */
    public function setIsValid(int $isValid)
    {
        $this->isValid = $isValid;
    }

}
