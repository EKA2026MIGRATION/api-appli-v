<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductSportLink
 *
 * @ORM\Table(name="product_sport_link", indexes={@ORM\Index(name="product_sport_link_sport_FK", columns={"sport_id"}), @ORM\Index(name="product_sport_link_product_FK", columns={"product_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductSportLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_sport_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productSportLinkId;

    /**
     * @var Sport
     *
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="products")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     * })
     */
    private $sport;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="sports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     * })
     */
    private $product;

    public function getProductSportLinkId(): ?int
    {
        return $this->productSportLinkId;
    }

    public function getSport(): ?Sport
    {
        return $this->sport;
    }

    public function setSport(?Sport $sport): self
    {
        $this->sport = $sport;

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
}
