<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pickup
 *
 * @ORM\Table(name="pickup")
 * @ORM\Entity(repositoryClass="App\Repository\PickupRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Pickup
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="pickup_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pickupId;

    /**
     * @var Registration
     *
     * @ORM\OneToOne(targetEntity="Registration")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="registration_id")
     */
    private $registration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="kind", type="string", length=8, nullable=true)
     */
    private $kind;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=35, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="postal", type="string", length=10, nullable=true)
     */
    private $postal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="string", length=256, nullable=true)
     */
    private $address;

    /**
     * @var float|null
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     */
    private $latitude;

    /**
     * @var float|null
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     */
    private $longitude;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $sortOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32)
     */
    private $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="status_change", type="datetime")
     */
    private $statusChange;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="places", type="integer", nullable=true)
     */
    private $places;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string")
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="validated", type="string", length=16)
     */
    private $validated;

    /**
     * @var Child
     *
     * @ORM\OneToOne(targetEntity="Child")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     */
    private $child;

    /**
     * @var Ride
     *
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="pickups")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="ride_id")
     */
    private $ride;

    /**
     * @var string
     *
     * @ORM\Column(name="sms_sent_data", type="string", nullable=true)
     */
    private $smsSentData;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payment_due", type="string", length=8, nullable=true)
     */
    private $paymentDue;


    /**
     * @var string|null
     *
     * @ORM\Column(name="payment_done", type="string", length=8, nullable=true)
     */
    private $paymentDone;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="last_day_of_week", type="date")
     */
    private $lastDayOfWeek;

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['start']) {
            $objectArray['start'] = $objectArray['start']->format('Y-m-d H:i');
        }
        if (null !== $objectArray['statusChange']) {
            $objectArray['statusChange'] = $objectArray['statusChange']->format('Y-m-d H:i:s');
        }

        if (null !== $objectArray['smsSentData']) {
            $objectArray['smsSentData'] = $this->getSmsSentData();
        }

        if(null !== $objectArray['registration']) {

            if($this->getRegistration()) {
                $categories = $this->getRegistration()->getProduct()->getCategories();
                $objectArray['category'] = $categories[0]->getCategory()->getName();
                $objectArray['category_link'] = $categories[0]->getCategory()->getPhoto();
            }


        }

        if (null !== $objectArray['lastDayOfWeek']) {
            $objectArray['lastDayOfWeek'] = $objectArray['lastDayOfWeek']->format('Y-m-d');
        }


        return $objectArray;
    }

    public function getPickupId(): ?int
    {
        return $this->pickupId;
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

    public function getKind(): ?string
    {
        return null !== $this->kind ? strtolower($this->kind) : null;
    }

    public function setKind(?string $kind): self
    {
        $this->kind = !empty($kind) && 'null' !== $kind ? strtolower($kind) : null;

        return $this;
    }

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getSmsSentData(): ?Array
    {
        if($this->smsSentData == null) return null;
        return unserialize($this->smsSentData);
    }

    public function setSmsSentData(Array $smsSentData): self
    {
        $this->smsSentData = serialize($smsSentData);

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(?string $postal): self
    {
        $this->postal = $postal;

        return $this;
    }

    public function getAddressGeocoding(): ?string
    {
        return $this->address;

    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

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

    public function getStatusChange(): ?DateTimeInterface
    {
        return $this->statusChange;
    }

    public function setStatusChange(?DateTimeInterface $statusChange): self
    {
        $this->statusChange = $statusChange;

        return $this;
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(?int $places): self
    {
        $this->places = $places;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getValidated(): ?string
    {
        return null !== $this->validated ? strtolower($this->validated) : null;
    }

    public function setValidated(?string $validated): self
    {
        $this->validated = !empty($validated) && 'null' !== $validated ? strtolower($validated) : null;

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

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(?Ride $ride): self
    {
        $this->ride = $ride;

        return $this;
    }

    public function getPaymentDue(): ?string
    {
        return $this->paymentDue;
    }

    public function setPaymentDue(?string $paymentDue): self
    {
        $this->paymentDue = $paymentDue;

        return $this;
    }


    public function getPaymentDone(): ?string
    {
        return $this->paymentDone;
    }

    public function setPaymentDone(?string $paymentDone): self
    {
        $this->paymentDone = $paymentDone;

        return $this;
    }

    public function getLastDayOfWeek(): ?DateTimeInterface
    {
        return $this->lastDayOfWeek;
    }

    public function setLastDayOfWeek(?DateTimeInterface $date): self
    {
        $this->lastDayOfWeek = $date;

        return $this;
    }

}
