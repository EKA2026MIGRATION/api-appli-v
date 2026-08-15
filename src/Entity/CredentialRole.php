<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Credential Role
 *
 * @ORM\Table(name="credential_role")
 * @ORM\Entity(repositoryClass="App\Repository\CredentialRoleRepository")
 *
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class CredentialRole
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
     * @var string|null
     *
     * @ORM\Column(name="role", type="string", length=48, nullable=true)
     */
    private $role;


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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

}
