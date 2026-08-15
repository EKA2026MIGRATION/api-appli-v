<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * InvoiceProduct
 *
 * @ORM\Table(name="invoice_product")
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceProductRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class InvoiceProduct
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="invoice_product_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $invoiceProductId;

    /**
     * @var Invoice
     *
     * @ORM\ManyToOne(targetEntity="Invoice", inversedBy="invoiceProducts", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="invoice_id", referencedColumnName="invoice_id")
     * })
     */
    private $invoice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name_fr", type="string", length=128, nullable=true)
     */
    private $nameFr;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name_en", type="string", length=128, nullable=true)
     */
    private $nameEn;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_fr", type="string", nullable=true)
     */
    private $descriptionFr;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_en", type="string", nullable=true)
     */
    private $descriptionEn;

    /**
     * @var float|null
     *
     * @ORM\Column(name="price_ht", type="float", nullable=true)
     */
    private $priceHt;

    /**
     * @var float|null
     *
     * @ORM\Column(name="price_ttc", type="float", nullable=true)
     */
    private $priceTtc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var float|null
     *
     * @ORM\Column(name="total_ht", type="float", nullable=true)
     */
    private $totalHt;

    /**
     * @var float|null
     *
     * @ORM\Column(name="total_ttc", type="float", nullable=true)
     */
    private $totalTtc;

    /**
     * @ORM\OneToMany(targetEntity="InvoiceComponent", mappedBy="invoiceProduct")
     * @SWG\Property(ref=@Model(type=InvoiceComponent::class))
     */
    private $invoiceComponents;

    public function __construct()
    {
        $this->invoiceComponents = new ArrayCollection();
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        if (null !== $objectArray['descriptionFr']) {
            $objectArray['descriptionFr'] = unserialize($objectArray['descriptionFr']);
        }

        return $objectArray;
    }

    public function getInvoiceProductId(): ?int
    {
        return $this->invoiceProductId;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getNameFr(): ?string
    {
        return $this->nameFr;
    }

    public function setNameFr(?string $nameFr): self
    {
        $this->nameFr = $nameFr;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(?string $nameEn): self
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    public function getDescriptionFr(): ?string
    {
        return null !== $this->descriptionFr ? unserialize($this->descriptionFr) : null;
    }

    public function setDescriptionFr(?string $descriptionFr): self
    {
        $this->descriptionFr = $descriptionFr;

        return $this;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(?string $descriptionEn): self
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    public function getPriceHt(): ?float
    {
        return $this->priceHt;
    }

    public function setPriceHt(?float $priceHt): self
    {
        $this->priceHt = $priceHt;

        return $this;
    }

    public function getPriceTtc(): ?float
    {
        return $this->priceTtc;
    }

    public function setPriceTtc(?float $priceTtc): self
    {
        $this->priceTtc = $priceTtc;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalHt(): ?float
    {
        return $this->totalHt;
    }

    public function setTotalHt(?float $totalHt): self
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    public function getTotalTtc(): ?float
    {
        return $this->totalTtc;
    }

    public function setTotalTtc(?float $totalTtc): self
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    /**
     * @return Collection|InvoiceComponent[]
     */
    public function getInvoiceComponents(): Collection
    {
        return $this->invoiceComponents;
    }

    public function addInvoiceComponent(InvoiceComponent $invoiceComponent): self
    {
        if (!$this->invoiceComponents->contains($invoiceComponent)) {
            $this->invoiceComponents[] = $invoiceComponent;
            $invoiceComponent->setInvoiceProduct($this);
        }

        return $this;
    }

    public function removeInvoiceComponent(InvoiceComponent $invoiceComponent): self
    {
        if ($this->invoiceComponents->contains($invoiceComponent)) {
            $this->invoiceComponents->removeElement($invoiceComponent);
            // set the owning side to null (unless already changed)
            if ($invoiceComponent->getInvoiceProduct() === $this) {
                $invoiceComponent->setInvoiceProduct(null);
            }
        }

        return $this;
    }
}
