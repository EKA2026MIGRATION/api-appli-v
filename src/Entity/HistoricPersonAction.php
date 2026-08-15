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
use DateTime;
use DateTimeInterface;

/**
 * Blog
 *
 * @ORM\Table(name="historic_person_action")
 * @ORM\Entity(repositoryClass="App\Repository\HistoricPersonActionRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class HistoricPersonAction
{
    use CreationTrait;
    use UpdateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="person_id", nullable=true)
     */
    private $person;

    /**
     * @var string|null
     *
     * @ORM\Column(name="action", length=255 ,type="string")
     */
    private $action;



    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);


        if (null !== $objectArray['person']) {
            $objectArray['person'] = $this->getPerson()->getFullnameReverse();
        }

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

        /**
     * Get the value of action
     *
     * @return  string|null
     */ 
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the value of action
     *
     * @param  string|null  $action
     *
     * @return  self
     */ 
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }


    /**
     * Get the value of person
     *
     * @return  Person
     */ 
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set the value of person
     *
     * @param  Person  $person
     *
     * @return  self
     */ 
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }
}
