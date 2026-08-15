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
 * Stats
 *
 * @ORM\Table(name="stats_analyse_strat")
 * @ORM\Entity(repositoryClass="App\Repository\StatsAnalyseStratRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StatsAnalyseStrat
{

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
     * @ORM\Column (name="name", type="string", nullable=false)
     */
    private $name;


    /**
     * @var string|null
     * @ORM\Column (name="datas", type="string", nullable=true)
     */
    private $datas;

    /**
     * @var string|null
     * @ORM\Column (name="result", type="string", nullable=true)
     */
    private $result;

    /**
     * @var string|null
     * @ORM\Column (name="api_prompt", type="string", nullable=true)
     */
    private $apiPrompt;

    /**
     * @var string|null
     * @ORM\Column (name="api_result", type="string", nullable=true)
     */
    private $apiResult;

    /**
     * @var string|null
     * @ORM\Column (name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getDatas(): string
    {
        return $this->datas;
    }

    public function setDatas(string $datas)
    {
        $this->datas = $datas;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result)
    {
        $this->result = $result;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getApiPrompt(): string
    {
        return $this->apiPrompt;
    }

    public function setApiPrompt(string $apiPrompt)
    {
        $this->apiPrompt = $apiPrompt;
    }

    public function getApiResult(): string
    {
        return $this->apiResult;
    }

    public function setApiResult(string $apiResult)
    {
        $this->apiResult = $apiResult;
    }

}
