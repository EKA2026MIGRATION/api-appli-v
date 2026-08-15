<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;

/**
 * @ORM\Table(name="task_staff")
 * @ORM\Entity(repositoryClass="App\Repository\TaskStaffRepository")
 */
class TaskStaff
{

    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @var Task|null
     *
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=true)
     */
    private $task;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
    * @var string|null
    *
    * @ORM\Column(name="description", type="string", nullable=true)
    */
    private $description;

    /**
    * @var string|null
    *
    * @ORM\Column(name="type", type="string", nullable=true)
    */
    private $type;

    /**
     * @var Staff
     *
     * @ORM\OneToOne(targetEntity="Staff")
     * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="staff_id")
     */
    private $supervisor;


    /**
     * @ORM\Column(name="step", type="string", length=255, nullable=true)
     */
    private $step;

    /**
     * @ORM\Column(name="date_task", type="datetime", nullable=true)
     */
    private $dateTask;

    /**
     * @ORM\Column(name="date_limit", type="datetime", nullable=true)
     */
    private $dateLimit;

    /**
     *@var String | null (time in seconds)
     * @ORM\Column(name="duration", type="string", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(name="date_task_done", type="datetime", nullable=true)
     */
    private $dateTaskDone;

    /**
     * @var string|null
     * @ORM\Column(name="remote_address", type="string", length=255, nullable=true)
     */
    private $remoteAddress;


    /**
     * @ORM\Column(name="criticity", type="string", length=20, nullable=true)
     */
    private $criticity;

    /**
     *@var string[] | null ()
     *
     */
    private $arrayData;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTask(): ?\DateTimeInterface
    {
        return $this->dateTask;
    }

    public function setDateTask(\DateTimeInterface $dateTask): self
    {
        $this->dateTask = $dateTask;

        return $this;
    }

    public function getDateLimit(): ?\DateTimeInterface
    {
        return $this->dateLimit;
    }

    public function setDateLimit($dateLimit): self
    {
        $this->dateLimit = $dateLimit;

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

    public function getDateTaskDone(): ?\DateTimeInterface
    {
        return $this->dateTaskDone;
    }

    public function setDateTaskDone($dateTaskDone): self
    {
        $this->dateTaskDone = $dateTaskDone;

        return $this;
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

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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


    public function getStep(): ?string
    {
        return $this->step;
    }

    public function setStep(?string $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getSupervisor(): ?Staff
    {
        return $this->supervisor;
    }

    public function setSupervisor(?Staff $supervisor): self
    {
        $this->supervisor = $supervisor;

        return $this;
    }

    public function getRemoteAddress(): ?string
    {
      return $this->remoteAddress;
    }

    public function setRemoteAddress($remoteAddress): self
    {
      $this->remoteAddress = $remoteAddress;
      return $this;
    }

    public function getArrayData() {
          $task = [];
          $person = $this->getStaff()->getPerson();
          if($this->getSupervisor()) {
              $person2 = $this->getSupervisor()->getPerson();
              $supervisor = $person2->getFirstname().' '.$person2->getLastname();
              $supervisor_id = $this->getSupervisor()->getStaffId();
          } else {
             $supervisor = null;
             $supervisor_id = null;
          }

          $task = [
                    'id' => $this->getId(),
                    'type' => $this->getType(),
                    'name' => $this->getName(),
                    'description' => $this->getDescription(),
                    'step' => $this->getStep(),
                    'date_task' => $this->getDateTask()->format('Y-m-d H:i:s'),
                    'date_limit' => $this->getDateLimit()->format('Y-m-d H:i:s'),
                    'duration' => $this->getDuration(),
                    'staff' => $person->getFirstname().' '.$person->getLastname(),
                    'staff_id' => $this->getStaff()->getStaffId(),
                    'supervisor' => $supervisor,
                    'supervisor_id' => $supervisor_id
                  ];
        return $task;
    }


    /**
     * Converts the entity in an array
     */
    public function toArray()
    {

        $objectArray = get_object_vars($this);
        if (null !== $objectArray['staff']) {
              $objectArray['staff'] = $this->getStaff()->toArray("light");
              $objectArray['staff_fullname'] = $this->getStaff()->getPerson()->getFirstname().' '.$this->getStaff()->getPerson()->getLastname();

        }
        if (null !== $objectArray['supervisor']) {
              $objectArray['supervisor'] = $this->getSupervisor()->toArray("light");
              $objectArray['supervisor_fullname'] = $this->getSupervisor()->getPerson()->getFirstname().' '.$this->getSupervisor()->getPerson()->getLastname();

        }

        return $objectArray;
    }

    /**
     * Get the value of criticity
     */ 
    public function getCriticity()
    {
        return $this->criticity;
    }

    /**
     * Set the value of criticity
     *
     * @return  self
     */ 
    public function setCriticity($criticity)
    {
        $this->criticity = $criticity;

        return $this;
    }
}
