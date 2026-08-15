<?php
namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use c975L\UserBundle\Entity\UserLightAbstract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user", indexes={@ORM\Index(name="un_email", columns={"name", "email"})})
 * @ORM\Entity(repositoryClass="c975L\UserBundle\Repository\UserRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class User extends UserLightAbstract
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @ORM\OneToOne(targetEntity="UserPersonLink", mappedBy="user")
     */
    private $userPersonLink;

    /**
      * @var Collection
      * @ORM\OneToMany(targetEntity="Device", mappedBy="user")
      */
     private $devices;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
        $this->suppressed = false;
    }

    public function getUserPersonLink(): ?UserPersonLink
    {
        return $this->userPersonLink;
    }

    public function setUserPersonLink(?UserPersonLink $userPersonLink): self
    {
        $this->userPersonLink = $userPersonLink;

        return $this;
    }


    /**
      * @return Collection|Device[]
      */
     public function getDevices()
     {
         return $this->devices;
     }

     public function addDevice(Device $device): self
     {
         if (!$this->devices->contains($device)) {
             $this->devices[] = $device;
             $device->setUser($this);
         }

         return $this;
     }

     public function removeDevice(Device $device): self
     {
         if ($this->devices->contains($device)) {
             $this->devices->removeElement($device);
             // set the owning side to null (unless already changed)
             if ($device->getUser() === $this) {
                 $device->setUser(null);
             }
         }

         return $this;
     }

     public function toArray()
     {
         $datas = parent::toArray();
         if($this->getDevices()) {
             foreach($this->getDevices() as $device) {
                 $datas['devices'][] = $device->toArray();
             }
         }
         return $datas;
     }

}
