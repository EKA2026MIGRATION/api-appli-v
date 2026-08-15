<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Registration.
 *
 * @ORM\Table(name="registration")
 * @ORM\Entity(repositoryClass="App\Repository\RegistrationRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Registration
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="registration_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $registrationId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="registration", type="datetime", nullable=true)
     */
    private $registration;

    /**
     * @var Child
     *
     * @ORM\OneToOne(targetEntity="Child")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     */
    private $child;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     */
    private $person;

    /**
     * @var Product
     *
     * @ORM\OneToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     */
    private $product;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_id", type="integer", nullable=true)
     */
    private $invoice;

    /**
     * @var float|null
     *
     * @ORM\Column(name="payed", type="float", nullable=true)
     */
    private $payed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payment_type", type="string", length=16, nullable=true)
     */
    private $paymentType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payment_info", type="string", length=250, nullable=true)
     */
    private $paymentInfo;


    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=16, nullable=true)
     */
    private $status;

    /**
     * @var string|null
     *
     * @ORM\Column(name="preferences", type="string", nullable=true)
     */
    private $preferences;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sessions", type="string", nullable=true)
     */
    private $sessions;

    /**
     * @var Location
     *
     * @ORM\OneToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="location_id")
     */
    private $location;

    /**
     * @ORM\OneToMany(targetEntity="RegistrationSportLink", mappedBy="registration")
     * @SWG\Property(ref=@Model(type=Sport::class))
     */
    private $sports;

    /**
     * @var Transaction
     *
     * @ORM\ManyToOne(targetEntity="Transaction", inversedBy="registrations", cascade={"persist"})
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="transaction_id")
     */
    private $transaction;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="has_lunch", type="boolean", nullable=true)
     */
    private $hasLunch;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="has_transport", type="boolean", nullable=true  )
     */
    private $hasTransport;

 

     /**
     * @var Address
     *
     * @ORM\OneToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="address_id")
     */
    private $address;


    public function __construct()
    {
        $this->sports = new ArrayCollection();
    }

    /**
     * Converts the entity in an array.
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['registration']) {
            $objectArray['registration'] = $objectArray['registration']->format('Y-m-d');
        }
        if (null !== $objectArray['preferences']) {
            $objectArray['preferences'] = unserialize($objectArray['preferences']);
        }
        if (null !== $objectArray['sessions']) {
            $objectArray['sessions'] = unserialize($objectArray['sessions']);
        }

        return $objectArray;
    }

    public function getRegistrationId(): ?int
    {
        return $this->registrationId;
    }

    public function getRegistration(): ?DateTimeInterface
    {
        return $this->registration;
    }

    public function setRegistration(?DateTimeInterface $registration): self
    {
        $this->registration = $registration;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getInvoice(): ?int
    {
        return $this->invoice;
    }

    public function setInvoice(?int $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getPayed(): ?float
    {
        return $this->payed;
    }

    public function setPayed(float $payed): self
    {
        $this->payed = $payed;

        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(?string $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getPaymentInfo(): ?string
    {
        return $this->paymentInfo;
    }

    public function setPaymentInfo(?string $paymentInfo): self
    {
        $this->paymentInfo = $paymentInfo;

        return $this;
    }

    public function getStatus(): ?string
    {
        return null !== $this->status ? strtolower($this->status) : null;
    }

    public function setStatus(?string $status): self
    {
        $this->status = !empty($status) && 'null' !== $status ? strtolower($status) : null;

        return $this;
    }

    public function getPreferences()
    {
        return null !== $this->preferences ? unserialize($this->preferences) : null;
    }

    public function setPreferences($preferences): self
    {
        $this->preferences = $preferences;

        return $this;
    }

    public function getSessions()
    {
        return null !== $this->sessions ? unserialize($this->sessions) : null;
    }

    public function setSessions($sessions): self
    {
        $this->sessions = $sessions;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|RegistrationSportLink[]
     */
    public function getSports(): Collection
    {
        return $this->sports;
    }

    public function addSport(RegistrationSportLink $sport): self
    {
        if (!$this->sports->contains($sport)) {
            $this->sports[] = $sport;
            $sport->setRegistration($this);
        }

        return $this;
    }

    public function removeSport(RegistrationSportLink $sport): self
    {
        if ($this->sports->contains($sport)) {
            $this->sports->removeElement($sport);
            // set the owning side to null (unless already changed)
            if ($sport->getRegistration() === $this) {
                $sport->setRegistration(null);
            }
        }

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }


    /**
     * Get the value of hasLunch.
     *
     * @return bool
     */
    public function getHasLunch()
    {
        return $this->hasLunch;
    }

    /**
     * Set the value of hasLunch.
     *
     * @param bool $hasLunch
     *
     * @return self
     */
    public function setHasLunch(bool $hasLunch)
    {
        $this->hasLunch = $hasLunch;

        return $this;
    }

    /**
     * Get the value of hasTransport.
     *
     * @return bool
     */
    public function getHasTransport()
    {
        return $this->hasTransport;
    }

    /**
     * Set the value of hasTransport.
     *
     * @param bool $hasTransport
     *
     * @return self
     */
    public function setHasTransport(bool $hasTransport)
    {
        $this->hasTransport = $hasTransport;

        return $this;
    }

     /**
     * Get the value of address.
     *
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address.
     *
     * @param string|null $address
     *
     * @return self
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }



}
