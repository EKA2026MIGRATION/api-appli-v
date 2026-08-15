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
 * Blog
 *
 * @ORM\Table(name="booklet")
 * @ORM\Entity(repositoryClass="App\Repository\BookletRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class Booklet
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=250, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sport", type="string", length=250, nullable=true)
     */
    private $sport;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

        /**
     * @var int
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="BookletBoard", mappedBy="booklet", cascade={"persist"})
     * @SWG\Property(ref=@Model(type=BookletBoard::class))
     */
    private $boards;

    public function __construct()
    {
        $this->boards = new ArrayCollection();
        if(!$this->getIsActive()) $this->setIsActive(1);
    
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

         //Specific data
         if (null !== $objectArray['boards']) {
             $boardsArray = [];
             foreach($this->getBoards() as $board) {

                $boardsArray[] = $board->toArray();

             }
            $objectArray['boards'] = $boardsArray;
        }

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Get the value of name
     *
     * @return  string|null
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string|null  $name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return  string|null
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string|null  $description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of isActive
     *
     * @return  boolean
     */ 
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set the value of isActive
     *
     * @param  boolean  $isActive
     *
     * @return  self
     */ 
    public function setIsActive(int $isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

     /**
     * @return Collection|BookletBoard[]
     */
    public function getBoards(): Collection
    {
        return $this->boards;
    }

    public function addBoard($board): self
    {
        if (!$this->boards->contains($board)) {
            $this->boards[] = $board;
            $board->setBooklet($this);
        }

        return $this;
    }

    public function removeBoard($board): self
    {
        if ($this->boards->contains($board)) {
            $this->boards->removeElement($board);
            // set the owning side to null (unless already changed)
            if ($board->getBooklet() === $this) {
                $board->setBooklet(null);
            }
        }

        return $this;
    }


    /**
     * Get the value of sport
     *
     * @return  string|null
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * Set the value of sport
     *
     * @param  string|null  $sport
     *
     * @return  self
     */
    public function setSport($sport)
    {
        $this->sport = $sport;

        return $this;
    }
}
