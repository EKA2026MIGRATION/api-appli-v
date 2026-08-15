<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegistrationSportLink
 *
 * @ORM\Table(name="registration_sport_link", indexes={@ORM\Index(name="registration_sport_link_sport_FK", columns={"sport_id"}), @ORM\Index(name="registration_sport_link_registration_FK", columns={"registration_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RegistrationSportLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="registration_sport_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $registrationSportLinkId;

    /**
     * @var Sport
     *
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="registrations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sport_id", referencedColumnName="sport_id")
     * })
     */
    private $sport;

    /**
     * @var Registration
     *
     * @ORM\ManyToOne(targetEntity="Registration", inversedBy="sports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registration_id", referencedColumnName="registration_id")
     * })
     */
    private $registration;

    public function getRegistrationSportLinkId(): ?int
    {
        return $this->registrationSportLinkId;
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

    public function getRegistration(): ?Registration
    {
        return $this->registration;
    }

    public function setRegistration(?Registration $registration): self
    {
        $this->registration = $registration;

        return $this;
    }
}
