<?php

namespace App\Entity\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * SuppressionTrait class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
trait SuppressionTrait
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="suppressed", type="boolean")
     */
    private $suppressed;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(name="suppressed_at", type="datetime", nullable=true)
     */
    private $suppressedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="suppressed_by", type="integer")
     */
    private $suppressedBy;

    public function getSuppressed(): ?bool
    {
        return $this->suppressed;
    }

    public function setSuppressed(?bool $suppressed): self
    {
        $this->suppressed = $suppressed;

        return $this;
    }

    public function getSuppressedAt(): ?DateTimeInterface
    {
        return $this->suppressedAt;
    }

    public function setSuppressedAt(?DateTimeInterface $suppressedAt): self
    {
        $this->suppressedAt = $suppressedAt;

        return $this;
    }

    public function getSuppressedBy(): ?int
    {
        return $this->suppressedBy;
    }

    public function setSuppressedBy(?int $suppressedBy): self
    {
        $this->suppressedBy = $suppressedBy;

        return $this;
    }
}
