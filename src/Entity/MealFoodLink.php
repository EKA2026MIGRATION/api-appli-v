<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MealFoodLink
 *
 * @ORM\Table(name="meal_food_link", indexes={@ORM\Index(name="meal_food_link_food_FK", columns={"food_id"}), @ORM\Index(name="meal_food_link_meal_FK", columns={"meal_id"})})
 * @ORM\Entity
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class MealFoodLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="meal_food_link_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mealFoodLinkId;

    /**
     * @var Food
     *
     * @ORM\ManyToOne(targetEntity="Food", inversedBy="meals")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="food_id", referencedColumnName="food_id")
     * })
     */
    private $food;

    /**
     * @var Meal
     *
     * @ORM\ManyToOne(targetEntity="Meal", inversedBy="foods")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="meal_id", referencedColumnName="meal_id")
     * })
     */
    private $meal;

    public function getMealFoodLinkId(): ?int
    {
        return $this->mealFoodLinkId;
    }

    public function getFood(): ?Food
    {
        return $this->food;
    }

    public function setFood(?Food $food): self
    {
        $this->food = $food;

        return $this;
    }

    public function getMeal(): ?Meal
    {
        return $this->meal;
    }

    public function setMeal(?Meal $meal): self
    {
        $this->meal = $meal;

        return $this;
    }
}
