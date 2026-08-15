<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 *
 * @author Sandy Razafitrimo
 */
class Notification
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
     * @ORM\OneToMany(targetEntity="NotificationPersonLink", mappedBy="notification")
     */
    private $notificationPersonLinks;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_notification", type="datetime", nullable=true)
     */
    private $dateNotification;

    /**
     * @var String|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

        /**
     * @var String|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

        /**
     * @var String|null
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    public function __construct()
    {
        $this->notificationPersonLinks = new ArrayCollection();
    }

   

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['dateNotification']) {
            $objectArray['dateNotification'] = $objectArray['dateNotification']->format('Y-m-d H:i:s');
        }

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getDateNotification(): ?\DateTimeInterface
    {
        return $this->dateNotification;
    }

    public function setDateNotification(?\DateTimeInterface $date): self
    {
        $this->dateNotification = $date;

        return $this;
    }

     /**
     * @return Collection|NotificationPersonLink[]
     */
    public function getNotificationPersonLinks(): Collection
    {
        return $this->notificationPersonLinks;
    }

    public function addNotificationPersonLink(NotificationPersonLink $notificationPersonLink): self
    {
        if (!$this->notificationPersonLinks->contains($notificationPersonLink)) {
            $this->notificationPersonLinks[] = $notificationPersonLink;
            $notificationPersonLink->setNotification($this);
        }

        return $this;
    }

    public function removeNotificationPersonLink(NotificationPersonLink $notificationPersonLink): self
    {
        if ($this->notificationPersonLinks->contains($notificationPersonLink)) {
            $this->notificationPersonLinks->removeElement($notificationPersonLink);
            // set the owning side to null (unless already changed)
            if ($notificationPersonLink->getNotification() === $this) {
                $notificationPersonLink->setNotification(null);
            }
        }

        return $this;
    }
  

    /**
     * Get the value of url
     *
     * @return  String|null
     */ 
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @param  String|null  $url
     *
     * @return  self
     */ 
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
