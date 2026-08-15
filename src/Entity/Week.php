<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Week
 *
 * @ORM\Table(name="week")
 * @ORM\Entity(repositoryClass="App\Repository\WeekRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Week
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="week_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $weekId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="kind", type="string", length=8, nullable=true)
     */
    private $kind;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=8, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=true)
     */
    private $name;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_start", type="date")
     */
    private $dateStart;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Season", inversedBy="weeks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="season_id", referencedColumnName="season_id")
     * })
     */
    private $season;

    /**
     * @var string|null
     *
     * @ORM\Column(name="group_name", type="string", length=60, nullable=true)
     */
    private $groupName;


    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['dateStart']) {
            $objectArray['dateStart'] = $objectArray['dateStart']->format('Y-m-d');
            $objectArray['dateEnd'] = $this->getDateStart()->add(new DateInterval('P7D'))->format('Y-m-d');
        }

        return $objectArray;
    }

    public function getWeekId(): ?int
    {
        return $this->weekId;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateStart(): ?DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(?DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(?string $name): self
    {
        $this->groupName = $name;

        return $this;
    }

    
}
