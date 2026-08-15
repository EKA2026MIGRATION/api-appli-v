<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ProductCancelledDate
 *
 * @ORM\Table(name="product_cancelled_date")
 * @ORM\Entity(repositoryClass="App\Repository\ProductCancelledDateRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductCancelledDate
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="product_cancelled_date_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productCancelledDateId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var Category
     *
     * @ORM\OneToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id")
     */
    private $category;

    /**
     * @var Product
     *
     * @ORM\OneToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     */
    private $product;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message_fr", type="string", length=256)
     */
    private $messageFr;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message_en", type="string", length=256)
     */
    private $messageEn;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['date']) {
            $objectArray['date'] = $objectArray['date']->format('Y-m-d');
        }

        return $objectArray;
    }

    public function getProductCancelledDateId(): ?int
    {
        return $this->productCancelledDateId;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getMessageFr(): ?string
    {
        return $this->messageFr;
    }

    public function setMessageFr(string $messageFr): self
    {
        $this->messageFr = $messageFr;

        return $this;
    }

    public function getMessageEn(): ?string
    {
        return $this->messageEn;
    }

    public function setMessageEn(string $messageEn): self
    {
        $this->messageEn = $messageEn;

        return $this;
    }
}
