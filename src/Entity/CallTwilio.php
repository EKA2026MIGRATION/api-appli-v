<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Blog
 *
 * @ORM\Table(name="call_twilio")
 * @ORM\Entity(repositoryClass="App\Repository\CallTwilioRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class CallTwilio
{
    use CreationTrait;
    use UpdateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(name="call_sid", type="string", length=250, nullable=false)
     * @SWG\Property(description="The call sid")
     * @SWG\Property(example="CA1234567890abcdef1234567890abcdef")
     */
    private $callSid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="number", type="string", length=250, nullable=true)
     */
    private $number;

    /**
     * @var string|null
     *
     * @ORM\Column(name="from_person", type="string", length=250, nullable=true)
     */
    private $fromPerson;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     */
    private $person;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="call_date", type="date")
     */
    private $callDate;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="call_time", type="date")
     */
    private $callTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="duration", type="string", length=10, nullable=true)
     */
    private $duration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=250, nullable=true)
     */
    private $status;

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);
        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCallSid(): ?string
    {
        return $this->callSid;
    }

    public function setCallSid(string $callSid): self
    {
        $this->callSid = $callSid;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getFromPerson(): ?string
    {
        return $this->fromPerson;
    }

    public function setFromPerson(?string $fromPerson): self
    {
        $this->fromPerson = $fromPerson;

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

    public function getCallDate(): ?\DateTimeInterface
    {
        return $this->callDate;
    }

    public function setCallDate(?\DateTimeInterface $callDate): self
    {
        $this->callDate = $callDate;

        return $this;
    }

    public function getCallTime(): ?\DateTimeInterface
    {
        return $this->callTime;
    }

    public function setCallTime(?\DateTimeInterface $callTime): self
    {
        $this->callTime = $callTime;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
    

}
