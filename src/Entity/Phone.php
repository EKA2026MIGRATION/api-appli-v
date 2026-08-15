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

/**
 * Phone
 *
 * @ORM\Table(name="phone")
 * @ORM\Entity(repositoryClass="App\Repository\PhoneRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Phone
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="phone_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $phoneId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=48, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="is_prefered", type="string", length=50, nullable=true)
     */
    private $isPrefered;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=35, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="PersonPhoneLink", mappedBy="phone")
     * @SWG\Property(ref=@Model(type=Person::class))
     */
    private $persons;

    public function __construct()
    {
        $this->persons = new ArrayCollection();
    }

    /**
     * Converts the entity in an array
     */
    public function toArray($type = "full")
    {
        $objectArray = get_object_vars($this);
        if($type == "light") {
            unset($objectArray['__initializer__']);
            unset($objectArray['__cloner__']);
            unset($objectArray['__isInitialized__']);
            unset($objectArray['createdAt']);
            unset($objectArray['createdBy']);
            unset($objectArray['updatedBy']);
            unset($objectArray['updatedAt']);
            unset($objectArray['suppressedAt']);
            unset($objectArray['suppressedBy']);
            unset($objectArray['suppressed']);

        }

        return $objectArray;
    }

    public function getPhoneId(): ?int
    {
        return $this->phoneId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsPrefered(): ?string
    {
        return $this->isPrefered;
    }

    public function setIsPrefered(?string $isPrefered): self
    {
        $this->isPrefered = $isPrefered;
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

    public function getPersons()
    {
        return $this->persons;
    }

    public function addPerson(PersonPhoneLink $person): self
    {
        if (!$this->persons->contains($person)) {
            $this->persons[] = $person;
            $person->setPhone($this);
        }

        return $this;
    }

    public function removePerson(PersonPhoneLink $person): self
    {
        if ($this->persons->contains($person)) {
            $this->persons->removeElement($person);
            // set the owning side to null (unless already changed)
            if ($person->getPhone() === $this) {
                $person->setPhone(null);
            }
        }

        return $this;
    }
}
