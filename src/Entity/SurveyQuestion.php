<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use App\Entity\SurveyChapter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Blog
 *
 * @ORM\Table(name="survey_question")
 * @ORM\Entity(repositoryClass="App\Repository\SurveyQuestionRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class SurveyQuestion
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
     * @var SurveyChapter|null
     *
     * @ORM\OneToOne(targetEntity="SurveyChapter")
     * @ORM\JoinColumn(name="chapter_id", referencedColumnName="id")
     */
    private $chapter;

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
     * Get the value of chapter
     *
     * @return  SurveyChapter|null
     */
    public function getChapter()
    {
        return $this->chapter;
    }

    /**
     * Set the value of chapter
     *
     * @param  SurveyChapter|null  $chapter
     *
     * @return  self
     */
    public function setChapter(?SurveyChapter $chapter)
    {
        $this->chapter = $chapter;

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
