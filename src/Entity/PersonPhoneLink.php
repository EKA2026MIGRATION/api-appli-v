<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PersonPhoneLink
 *
 * @ORM\Table(name="person_phone_link", indexes={@ORM\Index(name="person_phone_link_phone_FK", columns={"phone_id"}), @ORM\Index(name="person_phone_link_person_FK", columns={"person_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PersonPhoneLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="person_phone_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $personPhoneLinkId;

    /**
     * @var Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone", inversedBy="persons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="phone_id", referencedColumnName="phone_id")
     * })
     */
    private $phone;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="phones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     * })
     */
    private $person;

    public function getPersonPhoneLinkId(): ?int
    {
        return $this->personPhoneLinkId;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function setPhone(?Phone $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPerson(): ?Person
    {
        if(!$this->person) return null;
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }
}
