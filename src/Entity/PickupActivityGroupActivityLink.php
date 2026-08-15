<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PickupActivityGroupActivityLink
 *
 * @ORM\Table(name="pickup_activity_group_activity_link", indexes={@ORM\Index(name="pickup_activity_group_activity_link_group_link_FK", columns={"group_activity_id"}), @ORM\Index(name="pickup_activity_group_activity_link_pickup_activity_link_FK", columns={"pickup_activity_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupActivityGroupActivityLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="pickup_activity_group_activity_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pickupActivityGroupActivityLinkId;

    /**
     * @var PickupActivity
     *
     * @ORM\ManyToOne(targetEntity="PickupActivity", inversedBy="groupActivities")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pickup_activity_id", referencedColumnName="pickup_activity_id")
     * })
     */
    private $pickupActivity;

    /**
     * @var GroupActivity
     *
     * @ORM\ManyToOne(targetEntity="GroupActivity", inversedBy="pickupActivities")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_activity_id", referencedColumnName="group_activity_id")
     * })
     */
    private $groupActivity;

    public function getPickupActivityGroupActivityLinkId(): ?int
    {
        return $this->pickupActivityGroupActivityLinkId;
    }

    public function getPickupActivity(): ?PickupActivity
    {
        return $this->pickupActivity;
    }

    public function setPickupActivity(?PickupActivity $pickupActivity): self
    {
        $this->pickupActivity = $pickupActivity;

        return $this;
    }

    public function getGroupActivity(): ?GroupActivity
    {
        return $this->groupActivity;
    }

    public function setGroupActivity(?GroupActivity $groupActivity): self
    {
        $this->groupActivity = $groupActivity;

        return $this;
    }
}
