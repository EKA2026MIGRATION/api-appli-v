<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use App\Entity\BookletBoard;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Blog
 *
 * @ORM\Table(name="booklet_item")
 * @ORM\Entity(repositoryClass="App\Repository\BookletItemRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletItem
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
     * @var BookletBoard|null
     *
     * @ORM\OneToOne(targetEntity="BookletBoard")
     * @ORM\JoinColumn(name="board_id", referencedColumnName="id")
     */
    private $board;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=250, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;


    /**
     * @var int|null
     *
     * @ORM\Column(name="scale", type="integer", nullable=true)
     */
    private $scale;


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
     * Get the value of board
     *
     * @return  BookletBoard|null
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Set the value of board
     *
     * @param  BookletBoard|null  $board
     *
     * @return  self
     */
    public function setBoard(?BookletBoard $board)
    {
        $this->board = $board;

        return $this;
    }


    /**
     * Get the value of scale
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * Set the value of scale
     */
    public function setScale($scale): self
    {
        $this->scale = $scale;

        return $this;
    }
}
