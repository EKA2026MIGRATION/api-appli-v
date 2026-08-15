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
 * Meal
 *
 * @ORM\Table(name="meal", indexes={@ORM\Index(name="meal_child_FK", columns={"child_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\MealRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Meal
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="meal_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mealId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var Child
     *
     * @ORM\ManyToOne(targetEntity="Child")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     * })
     */
    private $child;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     * })
     */
    private $person;

    /**
     * @var string|null
     *
     * @ORM\Column(name="free_name", type="string", length=256, nullable=true)
     */
    private $freeName;

    /**
     * @ORM\OneToMany(targetEntity="MealFoodLink", mappedBy="meal")
     * @SWG\Property(ref=@Model(type=Food::class))
     */
    private $foods;

    public function __construct()
    {
        $this->foods = new ArrayCollection();
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['date']) {
            $objectArray['date'] = $objectArray['date']->format('Y-m-d');
        }

        return $objectArray;
    }

    public function getMealId(): ?int
    {
        return $this->mealId;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFreeName(): ?string
    {
        return $this->freeName;
    }

    public function setFreeName(?string $freeName): self
    {
        $this->freeName = $freeName;

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

    /**
     * @return Collection|MealFoodLink[]
     */
    public function getFoods()
    {
        return $this->foods;
    }

    public function addFood(MealFoodLink $food): self
    {
        if (!$this->foods->contains($food)) {
            $this->foods[] = $food;
            $food->setMeal($this);
        }

        return $this;
    }

    public function removeFood(MealFoodLink $food): self
    {
        if ($this->foods->contains($food)) {
            $this->foods->removeElement($food);
            // set the owning side to null (unless already changed)
            if ($food->getMeal() === $this) {
                $food->setMeal(null);
            }
        }

        return $this;
    }
}
