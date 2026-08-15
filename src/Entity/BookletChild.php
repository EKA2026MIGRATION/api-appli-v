<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Blog
 *
 * @ORM\Table(name="booklet_child")
 * @ORM\Entity(repositoryClass="App\Repository\BookletChildRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletChild
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
     * @var Child
     *
     * @ORM\OneToOne(targetEntity="Child")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="child_id")
     */
    private $child;

        /**
     * @var Staff
     *
     * @ORM\OneToOne(targetEntity="Staff")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id")
     */
    private $staff;

    /**
     * @ORM\Column(name="date_evaluation", type="datetime", nullable=true)
     */
    private $dateEvaluation;


    /**
    * @var string|null
    *
    * @ORM\Column(name="comment", type="string", nullable=true)
    */
    private $comment;

    /**
    * @var string|null
    *
    * @ORM\Column(name="status", type="string", nullable=true)
    */
    private $status;



   
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Get the value of child
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set the value of child
     */
    public function setChild($child): self
    {
        $this->child = $child;

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
    public function setBooklet(Booklet $booklet)
    {
        $this->booklet = $booklet;

        return $this;
    }

    public function getDateEvaluation(): ?\DateTime
    {
        
        return $this->dateEvaluation;
    }

    public function setDateEvaluation($dateEvaluation): self
    {
        if (!$dateEvaluation instanceof DateTime) {
            $dateEvaluation = new DateTime($dateEvaluation);
        }
        $this->dateEvaluation = $dateEvaluation;
        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  string|null
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  string|null  $status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of comment
     *
     * @return  string|null
     */ 
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the value of comment
     *
     * @param  string|null  $comment
     *
     * @return  self
     */ 
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);


        if($objectArray['booklet'] !== null) {
            $objectArray['booklet'] = $this->getBooklet()->toArray();
        }

        if($objectArray['child'] !== null) {
            $objectArray['child'] = $this->getChild()->toArray();
        }


        if($objectArray['staff'] !== null) {
            $objectArray['staff'] = $this->getStaff()->toArray();
        }

        if($objectArray['dateEvaluation'] !== null) {
            $objectArray['dateEvaluation'] = $this->getDateEvaluation()->format('Y-m-d');
        }


        return $objectArray;
    }

    /**
     * Get the value of staff
     *
     * @return  Staff
     */ 
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     * Set the value of staff
     *
     * @param  Staff  $staff
     *
     * @return  self
     */ 
    public function setStaff(Staff $staff)
    {
        $this->staff = $staff;

        return $this;
    }
}
