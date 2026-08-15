<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Credential Staff
 *
 * @ORM\Table(name="credential_staff")
 * @ORM\Entity(repositoryClass="App\Repository\CredentialStaffRepository")
 *
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class CredentialStaff
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Credential
     *
     * @ORM\OneToOne(targetEntity="Credential")
     * @ORM\JoinColumn(name="credential_id", referencedColumnName="id")
     */
    private $credential;

    /**
     * @var Staff
     *
     * @ORM\OneToOne(targetEntity="Staff")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id")
     */
    private $staff;



    public function getId(): ?int
    {
        return $this->id;
    }

   
    public function getCredential(): ?Credential
    {
        return $this->credential;
    }

    public function setCredential(?Credential $credential): self
    {
        $this->credential = $credential;

        return $this;
    }

    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;

        return $this;
    }

}
