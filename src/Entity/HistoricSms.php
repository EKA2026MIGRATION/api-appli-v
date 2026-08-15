<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use DateTime;
use DateTimeInterface;

/**
 * Blog
 *
 * @ORM\Table(name="historic_sms")
 * @ORM\Entity(repositoryClass="App\Repository\HistoricSmsRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class HistoricSms
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
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="string")
     */
    private $content;


    /**
     * @var string|null
     *
     * @ORM\Column(name="signature", type="string")
     */
    private $signature;

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
     * @var string|null
     *
     * @ORM\Column(name="status", length=64 ,type="string")
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="unicode", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $unicode;


    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

          //Specific data
        if (null !== $objectArray['dateSended']) {
            $objectArray['dateSended'] = $objectArray['dateSended']->format('Y-m-d');
        }
        if (null !== $objectArray['timeSended']) {
            $objectArray['timeSended'] = $objectArray['timeSended']->format('H:i:s');
        }

        return $objectArray;
    }

    public function setId($id): ?int
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): self
    {
        $this->signature = $signature;
        return $this;
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


    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
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
     * Get the value of unicode
     *
     * @return  int
     */
    public function getUnicode()
    {
        return $this->unicode;
    }
    /**
     * Set the value of unicode
     *
     * @param  int  $unicode
     *
     * @return  self
     */

    public function setUnicode($unicode)
    {
        $this->unicode = $unicode;

        return $this;
    }
}
