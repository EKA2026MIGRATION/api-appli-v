<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * ChallengeChildResult
 *
 * @ORM\Table(name="challenge_child_result")
 * @ORM\Entity(repositoryClass="App\Repository\ChallengeChildResultRepository")
 *
 * @author Sandy
 */
class ChallengeChildResult
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
     * @var Child
     *
     * @ORM\OneToOne(targetEntity="Child")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     */
    private $child;

    /**
     * @var Season
     *
     * @ORM\OneToOne(targetEntity="Season")
     * @ORM\JoinColumn(name="season_id", referencedColumnName="season_id")
     */
    private $season;

    /**
     * @var string|null
     *
     * @ORM\Column(name="card_point", type="string", nullable=true)
     */
    private $cardPoint;

    /**
     * @var string|null
     *
     * @ORM\Column(name="details", type="string", nullable=true)
     */
    private $details;

    /**
     * @var string|null
     *
     * @ORM\Column(name="card_type", type="string", nullable=true)
     */
    private $cardType;


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

    public function getChild(): Child
    {
        return $this->child;
    }

    public function setChild(Child $child): self
    {
        $this->child = $child;
        return $this;

    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function setSeason(Season $season): self
    {
        $this->season = $season;
        return $this;

    }

    public function getCardPoint(): ?string
    {
        return $this->cardPoint;
    }

    public function setCardPoint(?string $cardPoint): self
    {
        $this->cardPoint = $cardPoint;
        return $this;

    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;
        return $this;

    }

    public function getCardType(): ?string
    {
        return $this->cardType;
    }

    public function setCardType(?string $cardType): self
    {
        $this->cardType = $cardType;
        return $this;

    }

}
