<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use DateTime;
use DateTimeInterface;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Person
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="person_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $personId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="firstname", type="string", length=64, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=64, nullable=false)
     */
    private $lastname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo", type="string", length=256, nullable=true)
     */
    private $photo;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="birthdate", type="date", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\OneToMany(targetEntity="PersonAddressLink", mappedBy="person")
     * @SWG\Property(ref=@Model(type=Address::class))
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity="PersonPhoneLink", mappedBy="person")
     * @SWG\Property(ref=@Model(type=Phone::class))
     */
    private $phones;

    /**
     * @ORM\OneToMany(targetEntity="ChildPersonLink", mappedBy="person")
     * @SWG\Property(ref=@Model(type=Child::class))
     */
    private $children;

    /**
     * @var string|null
     * Not mapped (for documentation), added from PersonService->toArray()
     */
    private $identifier;

    /**
     * @var string|null
     * Not mapped (for documentation), added from PersonService->toArray()
     */
    private $email;


    /**
     * @var int|null
     * Not mapped (for documentation), added from PersonService->toArray()
     */
    private $isBlackListed;

    /**
     * @ORM\OneToMany(targetEntity="PersonPersonLink", mappedBy="person")
     * @SWG\Property(ref=@Model(type=Person::class))
     */
    private $relations;

    /**
     * @ORM\OneToMany(targetEntity="PersonPersonLink", mappedBy="related")
     * @SWG\Property(ref=@Model(type=Person::class))
     */
    private $related;

    /**
     * @ORM\OneToOne(targetEntity="UserPersonLink", mappedBy="person", cascade={"persist"})
     */
    private $userPersonLink;

    /**
     * @ORM\OneToMany(targetEntity="NotificationPersonLink", mappedBy="person", cascade={"persist"})
     * @SWG\Property(ref=@Model(type=Notification::class))
     */
    private $notificationPersonLinks;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->phones = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->relations = new ArrayCollection();
        $this->related = new ArrayCollection();
    }

    /**
     * Converts the entity in an array
     */
    public function toArray($type = "full")
    {
        $objectArray = get_object_vars($this);
        if($this->getUserPersonLink() !== null && $this->getUserPersonLink()->getUser() !== null) {
            $objectArray['myIdentifier'] = $this->getUserPersonLink()->getUser()->getIdentifier();
            $objectArray['roles'] = $this->getUserPersonLink()->getUser()->getRoles();
            $objectArray['email'] = $this->getUserPersonLink()->getUser()->getEmail();
            $objectArray['user_id'] = $this->getUserPersonLink()->getUser()->getId();
        }


        if($this->getBirthdate()) {
            $objectArray['birthdate'] = $this->getBirthdate()->format('Y-m-d');
        }

        //$objectArray['phones'] = "100000";

        foreach($this->getPhones() as $link) {
            $objectArray['phones'] = $link->getPhone()->toArray($type);
        }

        if($type == "light") {
           if(isset($objectArray['__initializer__'])) unset($objectArray['__initializer__']);
           if(isset($objectArray['__cloner__'])) unset($objectArray['__cloner__']);
           if(isset($objectArray['__isInitialized__'])) unset($objectArray['__isInitialized__']);
           if(isset($objectArray['createdAt'])) unset($objectArray['createdAt']);
           if(isset($objectArray['createdBy'])) unset($objectArray['createdBy']);
           if(isset($objectArray['updatedBy'])) unset($objectArray['updatedBy']);
           if(isset($objectArray['updatedAt'])) unset($objectArray['updatedAt']);
           if(isset($objectArray['suppressedAt'])) unset($objectArray['suppressedAt']);
           if(isset($objectArray['suppressedBy'])) unset($objectArray['suppressedBy']);
           if(isset($objectArray['suppressed'])) unset($objectArray['suppressed']);
           unset($objectArray['myIdentifier']);
           unset($objectArray['roles']);
        }



        return $objectArray;
    }

    public function getPersonId(): ?int
    {
        return $this->personId;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }


    public function getIsBlackListed(): ?int
    {
        return $this->isBlackListed;
    }

    public function setIsBlackListed(?int $value): self
    {
        $this->isBlackListed = $value;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullnameReverse() {
        return $this->getLastname().' '.$this->getFirstname();
    }

    public function getFullname() {
        return $this->getFirstname().' '.$this->getLastname();
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getBirthdate(): ?DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate)
    {
          if (!$birthdate instanceof DateTime) {
              $birthdate = new DateTime($birthdate);
          }

      $this->birthdate = $birthdate;

      return $this;
    }

    public function getAddresses()
    {
        return $this->addresses;
    }

    public function addAddress(PersonAddressLink $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setPerson($this);
        }

        return $this;
    }

    public function removeAddress(PersonAddressLink $address): self
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
            // set the owning side to null (unless already changed)
            if ($address->getPerson() === $this) {
                $address->setPerson(null);
            }
        }

        return $this;
    }

    public function getPhones()
    {
        return $this->phones;
    }

    public function addPhone(PersonPhoneLink $phone): self
    {
        if (!$this->phones->contains($phone)) {
            $this->phones[] = $phone;
            $phone->setPerson($this);
        }

        return $this;
    }

    public function removePhone(PersonPhoneLink $phone): self
    {
        if ($this->phones->contains($phone)) {
            $this->phones->removeElement($phone);
            // set the owning side to null (unless already changed)
            if ($phone->getPerson() === $this) {
                $phone->setPerson(null);
            }
        }

        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function addChild(ChildPersonLink $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setPerson($this);
        }

        return $this;
    }

    public function removeChild(ChildPersonLink $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getPerson() === $this) {
                $child->setPerson(null);
            }
        }

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRelations()
    {
        return $this->relations;
    }

    public function getRelated()
    {
        return $this->related;
    }

    public function getUserPersonLink(): ?UserPersonLink
    {
        return $this->userPersonLink;
    }

    public function setUserPersonLink(?UserPersonLink $userPersonLink): self
    {
        $this->userPersonLink = $userPersonLink;

        return $this;
    }

    public function addRelation(PersonPersonLink $relation): self
    {
        if (!$this->relations->contains($relation)) {
            $this->relations[] = $relation;
            $relation->setPerson($this);
        }

        return $this;
    }

    public function removeRelation(PersonPersonLink $relation): self
    {
        if ($this->relations->contains($relation)) {
            $this->relations->removeElement($relation);
            // set the owning side to null (unless already changed)
            if ($relation->getPerson() === $this) {
                $relation->setPerson(null);
            }
        }

        return $this;
    }

    public function addRelated(PersonPersonLink $related): self
    {
        if (!$this->related->contains($related)) {
            $this->related[] = $related;
            $related->setRelated($this);
        }

        return $this;
    }

    public function removeRelated(PersonPersonLink $related): self
    {
        if ($this->related->contains($related)) {
            $this->related->removeElement($related);
            // set the owning side to null (unless already changed)
            if ($related->getRelated() === $this) {
                $related->setRelated(null);
            }
        }

        return $this;
    }

        /**
     * @return Collection|NotificationPersonLink[]
     */
    public function getNotificationPersonLinks(): Collection
    {
        return $this->notificationPersonLinks;
    }

    public function addNotificationPersonLink(NotificationPersonLink $notificationPersonLink): self
    {
        if (!$this->notificationPersonLinks->contains($notificationPersonLink)) {
            $this->notificationPersonLinks[] = $notificationPersonLink;
            $notificationPersonLink->setPerson($this);
        }

        return $this;
    }

    public function removeNotificationPersonLink(NotificationPersonLink $notificationPersonLink): self
    {
        if ($this->notificationPersonLinks->contains($notificationPersonLink)) {
            $this->notificationPersonLinks->removeElement($notificationPersonLink);
            // set the owning side to null (unless already changed)
            if ($notificationPersonLink->getPerson() === $this) {
                $notificationPersonLink->setPerson(null);
            }
        }

        return $this;
    }

}
