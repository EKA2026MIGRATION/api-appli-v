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
 * Food
 *
 * @ORM\Table(name="food", indexes={@ORM\Index(name="food_child_FK", columns={"child_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\FoodRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Food
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="food_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $foodId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=256, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="kind", type="string", length=48, nullable=true)
     */
    private $kind;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32)
     */
    private $status;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo", type="string", length=256, nullable=true)
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="MealFoodLink", mappedBy="food")
     * @SWG\Property(ref=@Model(type=Meal::class))
     */
    private $meals;

    public function __construct()
    {
        $this->meals = new ArrayCollection();
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        return $objectArray;
    }

    public function getFoodId(): ?int
    {
        return $this->foodId;
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

    public function getKind(): ?string
    {
        return null !== $this->kind ? strtolower($this->kind) : null;
    }

    public function setKind(?string $kind): self
    {
        $this->kind = !empty($kind) && 'null' !== $kind ? strtolower($kind) : null;

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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|MealFoodLink[]
     */
    public function getMeals(): Collection
    {
        return $this->meals;
    }

    public function addMeal(MealFoodLink $meal): self
    {
        if (!$this->meals->contains($meal)) {
            $this->meals[] = $meal;
            $meal->setFood($this);
        }

        return $this;
    }

    public function removeMeal(MealFoodLink $meal): self
    {
        if ($this->meals->contains($meal)) {
            $this->meals->removeElement($meal);
            // set the owning side to null (unless already changed)
            if ($meal->getFood() === $this) {
                $meal->setFood(null);
            }
        }

        return $this;
    }
}
