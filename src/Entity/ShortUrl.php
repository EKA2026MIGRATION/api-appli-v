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

/**
 * Blog
 *
 * @ORM\Table(name="short_url")
 * @ORM\Entity(repositoryClass="App\Repository\ShortUrlRepository")
 *
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class ShortUrl
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
     * @var string|null
     *
     * @ORM\Column(name="new_url", type="string", nullable=true)
     */
    private $newUrl;

     /**
     * @var string|null
     *
     * @ORM\Column(name="original_url", type="string", nullable=true)
     */
    private $originalUrl;


         /**
     * @var string|null
     *
     * @ORM\Column(name="url_code", type="string", nullable=true)
     */
    private $urlCode;
  

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNewUrl(): ?string
    {
        return $this->newUrl;
    }

    public function setNewUrl(?string $newUrl): self
    {
        $this->newUrl = $newUrl;

        return $this;
    }

    public function getOriginalUrl(): ?string
    {
        return $this->originalUrl;
    }

    public function setOriginalUrl(?string $originalUrl): self
    {
        $this->originalUrl = $originalUrl;
        
        return $this;
    }

    public function getUrlCode(): ?string
    {
        return $this->urlCode;
    }

    public function setUrlCode(?string $urlCode): self
    {
        $this->urlCode = $urlCode;
        
        return $this;
    }

  
}
