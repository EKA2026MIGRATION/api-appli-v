<?php

namespace App\Entity;

use App\Entity\Traits\CreationTrait;
use App\Entity\Traits\SuppressionTrait;
use App\Entity\Traits\UpdateTrait;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Device
 *
 * @ORM\Table(name="device")
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class Device
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
    * @ORM\ManyToOne(targetEntity="User", inversedBy="devices")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    */
    private $user;

    /**
     * @var string|null
     *
     * @ORM\Column(name="identifier", type="text")
     */
    private $identifier;

    /**
     * @var string|null
     *
     * @ORM\Column(name="datas", type="string", length=255, nullable=true)
     */
    private $datas;

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        //Specific data
        if (null !== $objectArray['user']) {
            $objectArray['user'] = ['user_id' => $this->getUser()->getId(),'identifier' => $this->getUser()->getIdentifier()];
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

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getDatas(): ?string
    {
        return $this->datas;
    }

    public function setDatas(?string $datas): self
    {
        $this->datas = $datas;

        return $this;
    }

}
