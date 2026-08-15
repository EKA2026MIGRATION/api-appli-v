<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use \DateTimeInterface;
use App\Entity\SurveyBookletChild;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Blog
 *
 * @ORM\Table(name="booklet_child_answer")
 * @ORM\Entity(repositoryClass="App\Repository\BookletChildAnswerRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletChildAnswer
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
     * @var BookletChild
     *
     * @ORM\OneToOne(targetEntity="BookletChild")
     * @ORM\JoinColumn(name="booklet_child_id", referencedColumnName="id")
     */
    private $bookletChild;

    /**
     * @var string|null
     *
     * @ORM\Column(name="item_reference_id", type="string", length=250, nullable=true)
     */
    private $itemReferenceId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="answer", type="string", length=10, nullable=true)
     */
    private $answer;
   
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of booklet_child
     *
     * @return  BookletChild
     */ 
    public function getBookletChild()
    {
        return $this->bookletChild;
    }

    /**
     * Set the value of booklet_child
     *
     * @param  BookletChild  $booklet_child
     *
     * @return  self
     */ 
    public function setBookletChild(BookletChild $bookletChild)
    {
        $this->bookletChild = $bookletChild;

        return $this;
    }
    /**
     * Get the value of answer
     *
     * @return  string|null
     */ 
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set the value of answer
     *
     * @param  string|null  $answer
     *
     * @return  self
     */ 
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }


    /**
     * Get the value of itemReferenceId
     *
     * @return  string|null
     */ 
    public function getItemReferenceId()
    {
        return $this->itemReferenceId;
    }

    /**
     * Set the value of itemReferenceId
     *
     * @param  string|null  $itemReferenceId
     *
     * @return  self
     */ 
    public function setItemReferenceId($itemReferenceId)
    {
        $this->itemReferenceId = $itemReferenceId;

        return $this;
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        unset($objectArray['bookletChild']);

        return $objectArray;
    }
}
