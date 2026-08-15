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
 * @ORM\Table(name="booklet_board")
 * @ORM\Entity(repositoryClass="App\Repository\BookletBoardRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletBoard
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
     * @var Booklet
     *
     * @ORM\OneToOne(targetEntity="Booklet")
     * @ORM\JoinColumn(name="booklet_id", referencedColumnName="id")
     */
    private $booklet;

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
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


    /**
     * @var int|null
     *
     * @ORM\Column(name="order_board", type="integer", nullable=true)
     */
    private $orderBoard;


    /**
     * @var string|null
     *
     * @ORM\Column(name="photo1", type="string", length=256, nullable=true)
     */
    private $photo1;


    /**
     * @var string|null
     *
     * @ORM\Column(name="photo2", type="string", length=256, nullable=true)
     */
    private $photo2;

        /**
     * @var string|null
     *
     * @ORM\Column(name="icon", type="string", length=256, nullable=true)
     */
    private $icon;


    /**
     * @ORM\OneToMany(targetEntity="BookletItem", mappedBy="board", cascade={"persist"})
     * @SWG\Property(ref=@Model(type=BookletItem::class))
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        if(!$this->getIsActive()) $this->setIsActive(1);
    
    }


    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        if($objectArray['items'] !== null) {
            $itemsArray = [];
            foreach($this->getItems() as $item) {

                if($item->getSuppressed() == 1) continue;
                $itemsArray[] = $item->toArray();
            }

            $objectArray['items'] = $itemsArray;
        }

        unset($objectArray['booklet']);

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
     * @return Collection|BookletItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(BookletItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setBoard($this);
        }

        return $this;
    }

    public function removeItem(BookletItem $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getBoard() === $this) {
                $item->setBoard(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of booklet
     *
     * @return  Booklet
     */ 
    public function getBooklet()
    {
        return $this->booklet;
    }

    /**
     * Set the value of booklet
     *
     * @param  Booklet  $booklet
     *
     * @return  self
     */ 
    public function setBooklet($booklet)
    {
        $this->booklet = $booklet;

        return $this;
    }


    /**
     * Get the value of photo1
     *
     * @return  string|null
     */ 
    public function getPhoto1()
    {
        return $this->photo1;
    }

    /**
     * Set the value of photo1
     *
     * @param  string|null  $photo1
     *
     * @return  self
     */ 
    public function setPhoto1($photo1)
    {
        $this->photo1 = $photo1;

        return $this;
    }

    /**
     * Get the value of photo2
     *
     * @return  string|null
     */ 
    public function getPhoto2()
    {
        return $this->photo2;
    }

    /**
     * Set the value of photo2
     *
     * @param  string|null  $photo2
     *
     * @return  self
     */ 
    public function setPhoto2($photo2)
    {
        $this->photo2 = $photo2;

        return $this;
    }


    /**
     * Get the value of icon
     *
     * @return  string|null
     */ 
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set the value of icon
     *
     * @param  string|null  $photo2
     *
     * @return  self
     */ 
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the value of orderBoard
     *
     * @return  int|null
     */ 
    public function getOrderBoard()
    {
        return $this->orderBoard;
    }

    /**
     * Set the value of orderBoard
     *
     * @param  int|null  $orderBoard
     *
     * @return  self
     */ 
    public function setOrderBoard($orderBoard)
    {
        $this->orderBoard = $orderBoard;

        return $this;
    }
}
