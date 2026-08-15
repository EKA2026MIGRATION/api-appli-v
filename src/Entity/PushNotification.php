<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * PushNotification
 *
 * @ORM\Table(name="push_notification")
 * @ORM\Entity(repositoryClass="App\Repository\PushNotificationRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class PushNotification
{
    use CreationTrait;
    use UpdateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="invoice_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name_fr", type="string", length=128, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name_en", type="string", length=250, nullable=true)
     */
    private $message;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="int", length=2, nullable=true)
     */
    private $isOpen;


    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['user']) {
            $objectArray['user'] = $this->getUser()->toArray();
        }

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getIsOpen(): ?int
    {
        return $this->isOpen;
    }

    public function setIsOpen(?int $isOpen): self
    {
        $this->isOpen = $isOpen;

        return $this;
    }
}
