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
 * @ORM\Table(name="survey")
 * @ORM\Entity(repositoryClass="App\Repository\SurveyRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class Survey
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
     * @ORM\OneToMany(targetEntity="SurveyChapter", mappedBy="survey", cascade={"persist"})
     * @SWG\Property(ref=@Model(type=SurveyChapter::class))
     */
    private $chapters;

    public function __construct()
    {
        $this->chapters = new ArrayCollection();
        if(!$this->getIsActive()) $this->setIsActive(1);
    
    }

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);


        if($objectArray['chapters']) {
            $chapArray = [];
            foreach($this->getChapters() as $chapter) {
                $currentChapter = $chapter->toArray();
                unset($currentChapter['survey']);
                $chapArray[] = $currentChapter;
            }
            $objectArray['chapters'] = $chapArray;
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
     * @return Collection|SurveyChapter[]
     */
    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    public function addChapter(SurveyChapter $chapter): self
    {
        if (!$this->chapters->contains($chapter)) {
            $this->chapters[] = $chapter;
            $chapter->setSurvey($this);
        }

        return $this;
    }

    public function removeChapter(SurveyChapter $chapter): self
    {
        if ($this->chapters->contains($chapter)) {
            $this->chapters->removeElement($chapter);
            // set the owning side to null (unless already changed)
            if ($chapter->getSurvey() === $this) {
                $chapter->setSurvey(null);
            }
        }

        return $this;
    }
}
