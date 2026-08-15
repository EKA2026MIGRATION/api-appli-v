<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PersonPersonLink
 *
 * @ORM\Table(name="person_person_link", indexes={@ORM\Index(name="person_person_link_person_FK", columns={"person_id"}), @ORM\Index(name="person_person_link_person_FK_1", columns={"related_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PersonPersonLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="person_person_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $personPersonLinkId;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="relations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     * })
     */
    private $person;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="related")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="related_id", referencedColumnName="person_id")
     * })
     */
    private $related;

    /**
     * @var string
     *
     * @ORM\Column(name="relation", type="string", length=64, nullable=false)
     */
    private $relation;

    public function getPersonPersonLinkId(): ?int
    {
        return $this->personPersonLinkId;
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

    public function getRelated(): ?Person
    {
        return $this->related;
    }

    public function setRelated(?Person $related): self
    {
        $this->related = $related;

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
