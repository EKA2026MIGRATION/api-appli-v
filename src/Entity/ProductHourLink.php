<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProductHourLink
 *
 * @ORM\Table(name="product_hour_link", indexes={@ORM\Index(name="product_hour_link_product_FK", columns={"product_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductHourLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_hour_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productHourLinkId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="start", type="time")
     */
    private $start;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="end", type="time")
     */
    private $end;
   
    /**
     * @var string|null
     *
     * @ORM\Column(name="message_fr", type="string", length=128, nullable=true)
     */
    private $messageFr;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message_en", type="string", length=128, nullable=true)
     */
    private $messageEn;

        /**
     * @var boolean
     *
     * @ORM\Column(name="is_full", type="boolean", nullable=true)
     */
    private $isFull;


    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="hours")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     * })
     */
    private $product;

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['start']) {
            $objectArray['start'] = $objectArray['start']->format('H:i:s');
        }
        if (null !== $objectArray['end']) {
            $objectArray['end'] = $objectArray['end']->format('H:i:s');
        }

        return $objectArray;
    }

    public function getProductHourLinkId(): ?int
    {
        return $this->productHourLinkId;
    }

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?DateTimeInterface $end): self
    {
        $this->end = $end;

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


    public function getIsFull(): ?bool
    {
        return $this->isFull;
    }

    public function setIsFull(bool $isFull): self
    {
        $this->isFull = $isFull;

        return $this;
    }

    public function getMessageFr(): ?string
    {
        return $this->messageFr;
    }

    public function setMessageFr(?string $messageFr): self
    {
        $this->messageFr = $messageFr;

        return $this;
    }

    public function getMessageEn(): ?string
    {
        return $this->messageEn;
    }

    public function setMessageEn(?string $messageEn): self
    {
        $this->messageEn = $messageEn;

        return $this;
    }

}
