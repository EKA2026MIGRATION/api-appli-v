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
 * @ORM\Table(name="historic_sms_list")
 * @ORM\Entity(repositoryClass="App\Repository\HistoricSmsListRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class HistoricSmsList
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
     * @var Child
     *
     * @ORM\OneToOne(targetEntity="Child")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="child_id", nullable=true)
     */
    private $child;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone_name", length=64 ,type="string")
     */
    private $phoneName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone_number", length=64, type="string")
     */
    private $phoneNumber;

    /**
     * @var Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone")
     * @ORM\JoinColumn(name="phone_id", referencedColumnName="phone_id")
     */
    private $phone;

    /**
     * @var HistoricSms
     *
     * @ORM\ManyToOne(targetEntity="HistoricSms")
     * @ORM\JoinColumn(name="historic_sms_id", referencedColumnName="id")
     */
    private $historicSms;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_sended", type="date")
     */
    private $dateSended;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="time_sended", type="time")
     */
    private $timeSended;


    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        if (null !== $objectArray['child']) {

            $objectArray['childFullname'] = $this->getChild()->getFullname();
            $objectArray['childFullnameReverse'] = $this->getChild()->getFullnameReverse();
            $objectArray['childId'] = $this->getChild()->getChildId();
            unset($objectArray['child']);
        }

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSended(): ?DateTimeInterface
    {
        return $this->dateSended;
    }

    public function setDateSended(?DateTimeInterface $dateSended): self
    {
        $this->dateSended = $dateSended;

        return $this;
    }

    public function getTimeSended(): ?DateTimeInterface
    {
        return $this->timeSended;
    }
    

    public function setTimeSended(?DateTimeInterface $timeSended): self
    {
        $this->timeSended = $timeSended;

        return $this;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function setPhone(?Phone $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getHistoricSms(): ?HistoricSms
    {
        return $this->historicSms;
    }

    public function setHistoricSms(?HistoricSms $historicSms): self
    {
        $this->historicSms = $historicSms;

        return $this;
    }

    /**
     * Get the value of phoneNumber
     *
     * @return  string|null
     */ 
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set the value of phoneNumber
     *
     * @param  string|null  $phoneNumber
     *
     * @return  self
     */ 
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get the value of phoneName
     *
     * @return  string|null
     */ 
    public function getPhoneName()
    {
        return $this->phoneName;
    }

    /**
     * Set the value of phoneName
     *
     * @param  string|null  $phoneName
     *
     * @return  self
     */ 
    public function setPhoneName($phoneName)
    {
        $this->phoneName = $phoneName;

        return $this;
    }


    public function getChild(): ?Child
    {
        return $this->child;
    }

    public function setChild(?Child $child): self
    {
        $this->child = $child;

        return $this;
    }
}
