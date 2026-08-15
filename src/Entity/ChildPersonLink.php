<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChildPersonLink
 *
 * @ORM\Table(name="child_person_link", indexes={@ORM\Index(name="child_person_link_child_FK", columns={"child_id"}), @ORM\Index(name="child_person_link_person_FK", columns={"person_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ChildPersonLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="child_person_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $childPersonLinkId;

    /**
     * @var string
     *
     * @ORM\Column(name="relation", type="string", length=64, nullable=false)
     */
    private $relation;

    /**
     * @var Child
     *
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="persons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     * })
     */
    private $child;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     * })
     */
    private $person;

    public function getChildPersonLinkId(): ?int
    {
        return $this->childPersonLinkId;
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

    public function getChild(): ?Child
    {
        return $this->child;
    }

    public function setChild(?Child $child): self
    {
        $this->child = $child;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }
}
