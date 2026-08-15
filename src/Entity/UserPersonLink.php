<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPersonLink
 *
 * @ORM\Table(name="user_person_link", indexes={@ORM\Index(name="user_person_link_user_FK", columns={"id"}), @ORM\Index(name="user_person_link_person_FK", columns={"person_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class UserPersonLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_person_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userPersonLinkId;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="Person", inversedBy="userPersonLink")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     * })
     */
    private $person;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="userPersonLink")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function getUserPersonLinkId(): ?int
    {
        return $this->userPersonLinkId;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
