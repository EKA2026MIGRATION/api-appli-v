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
 * Mail
 *
 * @ORM\Table(name="mail")
 * @ORM\Entity(repositoryClass="App\Repository\MailRepository")
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class Mail
{
    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="mail_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mailId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="subject_fr", type="string", length=128, nullable=true)
     */
    private $subjectFr;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content_fr", type="string", nullable=true)
     */
    private $contentFr;

    /**
     * @var string|null
     *
     * @ORM\Column(name="subject_en", type="string", length=128, nullable=true)
     */
    private $subjectEn;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content_en", type="string", nullable=true)
     */
    private $contentEn;

    /**
     * Converts the entity in an array
     */
    public function toArray()
    {
        $objectArray = get_object_vars($this);

        return $objectArray;
    }

    public function getMailId(): ?int
    {
        return $this->mailId;
    }

    /**
     * Get the value of subjectFr
     *
     * @return  string|null
     */ 
    public function getSubjectFr()
    {
        return $this->subjectFr;
    }

    /**
     * Set the value of subjectFr
     *
     * @param  string|null  $subjectFr
     *
     * @return  self
     */ 
    public function setSubjectFr($subjectFr)
    {
        $this->subjectFr = $subjectFr;

        return $this;
    }

    /**
     * Get the value of contentFr
     *
     * @return  string|null
     */ 
    public function getContentFr()
    {
        return $this->contentFr;
    }

    /**
     * Set the value of contentFr
     *
     * @param  string|null  $contentFr
     *
     * @return  self
     */ 
    public function setContentFr($contentFr)
    {
        $this->contentFr = $contentFr;

        return $this;
    }

    /**
     * Get the value of subjectEn
     *
     * @return  string|null
     */ 
    public function getSubjectEn()
    {
        return $this->subjectEn;
    }

    /**
     * Set the value of subjectEn
     *
     * @param  string|null  $subjectEn
     *
     * @return  self
     */ 
    public function setSubjectEn($subjectEn)
    {
        $this->subjectEn = $subjectEn;

        return $this;
    }

    /**
     * Get the value of contentEn
     *
     * @return  string|null
     */ 
    public function getContentEn()
    {
        return $this->contentEn;
    }

    /**
     * Set the value of contentEn
     *
     * @param  string|null  $contentEn
     *
     * @return  self
     */ 
    public function setContentEn($contentEn)
    {
        $this->contentEn = $contentEn;

        return $this;
    }
}
