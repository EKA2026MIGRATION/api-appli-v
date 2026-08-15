<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupActivityStaffLink
 *
 * @ORM\Table(name="group_activity_staff_link", indexes={@ORM\Index(name="group_activity_staff_link_group_FK", columns={"group_activity_id"}), @ORM\Index(name="group_activity_staff_link_staff_FK", columns={"staff_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class GroupActivityStaffLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="group_activity_staff_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $GroupActivityStaffLinkId;

    /**
     * @var GroupActivity
     *
     * @ORM\ManyToOne(targetEntity="GroupActivity", inversedBy="staff")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_activity_id", referencedColumnName="group_activity_id")
     * })
     */
    private $groupActivity;

    /**
     * @var Staff
     *
     * @ORM\ManyToOne(targetEntity="Staff", inversedBy="groupActivities")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id")
     * })
     */
    private $staff;

    public function getGroupActivityStaffLinkId(): ?int
    {
        return $this->GroupActivityStaffLinkId;
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

    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;

        return $this;
    }
}
