<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use App\Entity\TaskStaff;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketRepository")
 */
class Ticket
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Staff
     *
     * @ORM\OneToOne(targetEntity="Staff")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id", nullable=true)
     */
    private $staff;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $persona;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @var Category
     *
     * @ORM\OneToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id", nullable=true)
     */
    private $category;

    /**
     * @var Location
     *
     * @ORM\OneToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="location_id", nullable=true)
     */
    private $location;

    /**
     * @var Rdv
     *
     * @ORM\OneToOne(targetEntity="Rdv")
     * @ORM\JoinColumn(name="rdv_id", referencedColumnName="id", nullable=true)
     */
    private $rdv;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $recall;


    /**
     * @ORM\Column(name= "has_been_treated", type="boolean", nullable=true)
     */
    private $hasBeenTreated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $originCall;

    /**
     * @var TaskStaff
     *
     * @ORM\OneToOne(targetEntity="TaskStaff")
     * @ORM\JoinColumn(name="task_staff_id", referencedColumnName="id", nullable=true)
     */
    private $taskStaff;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCall;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;

        return $this;
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

    public function getPersona(): ?string
    {
        return $this->persona;
    }

    public function setPersona(?string $persona): self
    {
        $this->persona = $persona;

        return $this;
    }


    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRecall(): ?bool
    {
        return $this->recall;
    }

    public function setRecall(?bool $recall): self
    {
        $this->recall = $recall;

        return $this;
    }

    public function getHasBeenTreated(): ?bool
    {
        return $this->hasBeenTreated;
    }

    public function setHasBeenTreated(?bool $hasBeenTreated): self
    {
        $this->hasBeenTreated = $hasBeenTreated;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }


    public function getOriginCall(): ?string
    {
        return $this->originCall;
    }

    public function setOriginCall(?string $originCall): self
    {
        $this->originCall = $originCall;

        return $this;
    }

    public function getDateCall(): ?\DateTimeInterface
    {
        return $this->dateCall;
    }

    public function setDateCall(?\DateTimeInterface $dateCall): self
    {
        $this->dateCall = $dateCall;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getRdv(): ?Rdv
    {
        return $this->rdv;
    }

    public function setRdv(?Rdv $rdv): self
    {
        $this->rdv = $rdv;

        return $this;
    }


    public function getTaskStaff(): ?TaskStaff
    {
        return $this->taskStaff;
    }

    public function setTaskStaff(?taskStaff $taskStaff): self
    {
        $this->taskStaff = $taskStaff;

        return $this;
    }



    /**
     * Converts the entity in an array
     */
    public function toArray($type = "full")
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['dateCall']) {
            $objectArray['dateCall'] = $objectArray['dateCall']->format('Y-m-d H:i:s');
        }

        if (null !== $objectArray['staff']) {
            $objectArray['staff'] = $this->getStaff()->toArray($type);
        }

        if (null !== $objectArray['rdv']) {
            $objectArray['rdv'] = $this->getRdv()->toArray();
        }

        if (null !== $objectArray['taskStaff']) {
            try {
                $objectArray['taskStaff'] = $this->getTaskStaff()->toArray();
            } catch(Exception $e) {
              $objectArray['taskStaff'] = ['message' => 'Task effacée par erreur'];
            }
        }

        if (null !== $objectArray['location']) {
            $objectArray['location'] = $this->getLocation()->toArray();
        }

        if (null !== $objectArray['category']) {
            $objectArray['category'] = $this->getCategory()->toArray();
        }


        return $objectArray;
    }

}
