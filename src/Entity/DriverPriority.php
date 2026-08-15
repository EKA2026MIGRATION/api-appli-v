<?php

namespace App\Entity;

/**
 * DriverPriority
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class DriverPriority
{
    /**
     * @var string|null
     */
    private $staff;

    /**
     * @var integer
     */
    private $priority;

    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}
