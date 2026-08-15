<?php

namespace App\Entity;

use App\Entity\Season;
use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Match
 *
 * @ORM\Table(name="foot_match_result")
 * @ORM\Entity(repositoryClass="App\Repository\FootMatchResultRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class FootMatchResult
{

    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;


    /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
     * @var Child
     *
     * @ORM\OneToOne(targetEntity="Child")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     */
    private $child;

    /**
     * @var FootMatch
     *
     * @ORM\ManyToOne(targetEntity="FootMatch", inversedBy="footMatchResults")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     */
    private $footMatch;

    /**
     * @var string|null
     * @ORM\Column(name="position", type="string", length=250, nullable=true)
     */
    private $position;

    /**
     * @var int|null
     * @ORM\Column(name="position_number", type="integer", nullable=true)
     */
    private $positionNumber;

    /**
     * @var int|null
     * @ORM\Column(name="goal", type="integer", nullable=true)
     */
    private $goal;

    /**
     * @var int|null
     * @ORM\Column(name="decisive_pass", type="integer", nullable=true)
     */
    private $decisivePass;

    /**
     * @var int|null
     * @ORM\Column(name="ballons_recuperes", type="integer", nullable=true)
     */
    private $ballonsRecuperes;

    /**
     * @var int|null
     * @ORM\Column(name="shots_saved", type="integer", nullable=true)
     */
    private $shotsSaved;

    /**
     * @var int|null
     * @ORM\Column(name="man_of_the_match", type="integer", nullable=true)
     */
    private $manOfTheMatch;

    /**
     * @var int|null
     * @ORM\Column(name="yellow_card", type="integer", nullable=true)
     */
    private $yellowCard;

    /**
     * @var int|null
     * @ORM\Column(name="red_card", type="integer", nullable=true)
     */
    private $redCard;


    /**
     * @var int|null
     * @ORM\Column(name="team", type="integer", nullable=true)
     */
    private $team;


    public function toArray() {
        $objectArray = get_object_vars($this);
        $objectArray['child_name'] = $this->child->getFullname();
        $objectArray['child_id'] = $this->child->getChildId();
        return $objectArray;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChild(): Child
    {
        return $this->child;
    }

    public function setChild(Child $child): self
    {
        $this->child = $child;
        return $this;

    }

    public function getFootMatch(): FootMatch
    {
        return $this->footMatch;
    }

    public function setFootMatch($footMatch): self
    {
        $this->footMatch = $footMatch;
        return $this;

    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;
        return $this;

    }

    public function getPositionNumber(): ?int
    {
        return $this->positionNumber;
    }

    public function setPositionNumber(?int $positionNumber): self
    {
        $this->positionNumber = $positionNumber;
        return $this;
    }

    public function getGoal(): ?int
    {
        return $this->goal;
    }

    public function setGoal(?int $goal): self
    {
        $this->goal = $goal;
        return $this;

    }

    public function getDecisivePass(): ?int
    {
        return $this->decisivePass;
    }

    public function setDecisivePass(?int $decisivePass): self
    {
        $this->decisivePass = $decisivePass;
        return $this;

    }

    public function getBallonsRecuperes(): ?int
    {
        return $this->ballonsRecuperes;
    }

    public function setBallonsRecuperes(?int $ballonsRecuperes): self
    {
        $this->ballonsRecuperes = $ballonsRecuperes;
        return $this;

    }

    public function getShotsSaved(): ?int
    {
        return $this->shotsSaved;
    }

    public function setShotsSaved(?int $shotsSaved): self
    {
        $this->shotsSaved = $shotsSaved;
        return $this;

    }

    public function getManOfTheMatch(): ?int
    {
        return $this->manOfTheMatch;
    }

    public function setManOfTheMatch(?int $manOfTheMatch): self
    {
        $this->manOfTheMatch = $manOfTheMatch;
        return $this;

    }

    public function getYellowCard(): ?int
    {
        return $this->yellowCard;
    }

    public function setYellowCard(?int $yellowCard): self
    {
        $this->yellowCard = $yellowCard;
        return $this;

    }

    public function getRedCard(): ?int
    {
        return $this->redCard;
    }

    public function setRedCard(?int $redCard): self
    {
        $this->redCard = $redCard;
        return $this;
    }

    public function getTeam(): ?int
    {
        return $this->team;
    }

    public function setTeam(?int $team): self
    {
        $this->team = $team;
        return $this;
    }


}
