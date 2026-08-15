<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChildChildLink
 *
 * @ORM\Table(name="child_child_link", indexes={@ORM\Index(name="child_child_link_child_FK", columns={"child_id"}), @ORM\Index(name="child_child_link_child_FK_1", columns={"sibling_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ChildChildLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="child_child_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $childChildLinkId;

    /**
     * @var Child
     *
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="siblings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     * })
     */
    private $child;

    /**
     * @var Child
     *
     * @ORM\ManyToOne(targetEntity="Child")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sibling_id", referencedColumnName="child_id")
     * })
     */
    private $sibling;

    /**
     * @var string
     *
     * @ORM\Column(name="relation", type="string", length=64, nullable=false)
     */
    private $relation;

    public function getChildChildLinkId(): ?int
    {
        return $this->childChildLinkId;
    }

    public function getChild(): ?Child
    {
        return $this->child;
    }

    public function setChild(?Child $child): self
    {
        $this->child = $child;

        return $this;
    }

    public function getSibling(): ?Child
    {
        return $this->sibling;
    }

    public function setSibling(?Child $sibling): self
    {
        $this->sibling = $sibling;

        return $this;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(?string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }
}
