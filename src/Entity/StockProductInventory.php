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
 * @ORM\Table(name="stock_product_inventory")
 * @ORM\Entity(repositoryClass="App\Repository\StockProductInventoryRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StockProductInventory
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
     * @var Object_
     *
     * @ORM\ManyToOne(targetEntity="StockProduct")
     * @ORM\JoinColumn(name="product_ref_id", referencedColumnName="id")
     */
    private $productRef;

    /**
     * @ORM\Column(name="date_inventory", type="datetime", nullable=true)
     */
    private $dateInventory;


    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=250, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;


    /**
     * @var String|null
     *
     * @ORM\Column(name="category_name", type="string", nullable=true)
     */
    private $categoryName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="minimum_stock", type="string", nullable=true)
     */
    private $minimumStock;

    /**
     * @var string|null
     *
     * @ORM\Column(name="unity", type="string", nullable=true)
     */
    private $unity;

    /**
     * @var string|null
     *
     * @ORM\Column(name="conditioning", type="string", nullable=true)
     */
    private $conditioning;


    /**
     * @var string|null
     *
     * @ORM\Column(name="current_stock", type="string", nullable=true)
     */
    private $currentStock;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price", type="string", nullable=true)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bar_code", type="string", nullable=true)
     */
    private $barCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image_url", type="string", nullable=true)
     */
    private $imageUrl;

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        if($objectArray['productRef']) $objectArray['productRef'] = $this->getProductRef()->toArray();
        $objectArray['dateInventory'] = $this->getDateInventory()->format('Y-m-d');
        $objectArray['restockLevel'] = $this->getProductRef()->getRestockLevel();

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
    public function getProductRef(): StockProduct
    {
        return $this->productRef;
    }

    /**
     * @param Object_ $productRef
     */
    public function setProductRef(StockProduct $productRef)
    {
        $this->productRef = $productRef;

        return $this;
    }


    public function getDateInventory(): ?\DateTimeInterface
    {
        return $this->dateInventory;
    }

    public function setDateInventory(\DateTimeInterface $dateInventory): self
    {
        $this->dateInventory = $dateInventory;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return  string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string|null  $description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|String
     */
    public function getCategoryName(): String
    {
        return $this->categoryName;
    }

    /**
     * @param null|String $categoryName
     */
    public function setCategoryName(String $categoryName)
    {
        $this->categoryName = $categoryName;
    }

    /**
     * @return null|string
     */
    public function getMinimumStock(): string
    {
        return $this->minimumStock;
    }

    /**
     * @param null|string $minimumStock
     */
    public function setMinimumStock(string $minimumStock)
    {
        $this->minimumStock = $minimumStock;
    }

    /**
     * @return null|string
     */
    public function getCurrentStock(): string
    {
        return $this->currentStock;
    }

    /**
     * @param null|string $currentStock
     */
    public function setCurrentStock(string $currentStock)
    {
        $this->currentStock = $currentStock;
    }

    /**
     * @return null|string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param null|string $price
     */
    public function setPrice(string $price)
    {
        $this->price = $price;
    }

    /**
     * @return null|string
     */
    public function getBarCode(): string
    {
        return $this->barCode;
    }

    /**
     * @param null|string $barCode
     */
    public function setBarCode(string $barCode)
    {
        $this->barCode = $barCode;
    }

    /**
     * @return null|string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @param null|string $imageUrl
     */
    public function setImageUrl(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return null|string
     */
    public function getUnity(): string
    {
        return $this->unity;
    }

    /**
     * @param null|string $unity
     */
    public function setUnity(string $unity)
    {
        $this->unity = $unity;
    }

    /**
     * @return null|string
     */
    public function getConditioning(): string
    {
        return $this->conditioning;
    }

    /**
     * @param null|string $conditioning
     */
    public function setConditioning(string $conditioning)
    {
        $this->conditioning = $conditioning;
    }



}
