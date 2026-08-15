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
 * Match
 *
 * @ORM\Table(name="foot_match")
 * @ORM\Entity(repositoryClass="App\Repository\FootMatchRepository")
 *
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class FootMatch
{

    use CreationTrait;
    use UpdateTrait;
    use SuppressionTrait;


    /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    private $id;


    /**
     * @var string|null
     *
     * @ORM\Column(name="day", type="date", nullable=true)
     */
    private $day;


    /**
     * @var string|null
     *
     * @ORM\Column(name="time", type="time", nullable=true)
     */
    private $time;

    /**
     * @var string|null
     *
     * @ORM\Column(name="referee", type="string", length=250, nullable=true)
     */
    private $referee;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=250, nullable=true)
     */
    private $name;

    /**
     * @var array|null
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     * @ORM\Column(name="location", type="string", length=250, nullable=true)
     *
     */
    private $location;

    /**
     * @var string|null
     * @ORM\Column(name="score", type="string", length=20, nullable=true)
     */
    private $score;

    /**
     * @var string|null
     * @ORM\Column(name="team1", type="string", length=250, nullable=true)
     *
     */
    private $team1;

    /**
     * @var string|null
     * @ORM\Column(name="team2", type="string", length=250, nullable=true)
     *
     */
    private $team2;

    /**
     * @var int|null
     * @ORM\Column(name="is_winner", type="integer", nullable=true)
     *
     */
    private $isWinner;


    /**
     * @var Season
     *
     * @ORM\OneToOne(targetEntity="Season")
     * @ORM\JoinColumn(name="season_id", referencedColumnName="season_id")
     */
    private $season;

    /**
     * @var Collection|FootMatchResult[]
     *
     * @ORM\OneToMany(targetEntity="FootMatchResult", mappedBy="footMatch")
     * @ORM\OrderBy({"team" = "ASC"})
     */
    private $footMatchResults;


    private $scoreTeam1;

    private $scoreTeam2;



    public function __construct()
    {
        $this->footMatchResults = new ArrayCollection();
    }

    public function  createScoreTeam() {


        if($this->getScore() !== null) {

            $scores = explode('-', $this->getScore());


            // check if isWinner == 0
            if ($this->getIsWinner() == 0) {
                $this->setScoreTeam1($scores[0]);
                $this->setScoreTeam2($scores[1]);
            } else {
                // check the greater between scoreTeam1 and scoreTeam2
                if ($scores[0] > $scores[1]) {
                    $winner = $scores[0];
                    $looser = $scores[1];
                } else {
                    $winner = $scores[1];
                    $looser = $scores[0];
                }

                // affect if isWinner == 1
                if ($this->getIsWinner() == 1) {
                    $this->setScoreTeam1($winner);
                    $this->setScoreTeam2($looser);
                } else {
                    $this->setScoreTeam1($looser);
                    $this->setScoreTeam2($winner);
                }
            }
        }
    }

    public function toArray() {

        $this->createScoreTeam();

        $objectArray = get_object_vars($this);

        $objectArray['season_name'] = $this->season->getName();
        $objectArray['season_id'] = $this->season->getSeasonId();
        $objectArray['day'] = $this->day->format('Y-m-d');
        $objectArray['time'] = $this->time->format('H:i');

        foreach($this->getFootMatchResults() as $result) {
            $objectArray['foot_match_results'][] = $result->toArray();
            $objectArray['players_team'.$result->getTeam()][] = ['id' => $result->getChild()->getChildId(), 'fullname' => $result->getChild()->getFullname(), 'team' => $result->getTeam()];
        };
        return $objectArray;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of day
     *
     * @return  string|null
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set the value of day
     *
     * @param  string|null  $day
     *
     * @return  self
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get the value of time
     *
     * @return  string|null
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set the value of time
     *
     * @param  string|null  $time
     *
     * @return  self
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    public function getReferee(): ?string
    {
        return $this->referee;
    }

    public function setReferee(?string $referee): self
    {
        $this->referee = $referee;
        return $this;
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

    public function getDescription(): ?array
    {
        return json_decode($this->description, true);
    }

    public function setDescription(?array $description): self
    {
        $this->description = json_encode($description);
        return $this;

    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;
        return $this;

    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): self
    {
        $this->score = $score;
        return $this;

    }

    public function getScoreTeam($team) {
        if($team == 1) {
            return $this->getScoreTeam1();
        } else {
            return $this->getScoreTeam2();
        }
    }

    public function getScoreTeam1() {
        return $this->scoreTeam1;
    }

    public function getScoreTeam2() {
        return $this->scoreTeam2;
    }

    public function setScoreTeam1($score) {
        $this->scoreTeam1 = $score;
        return $this;
    }

    public function setScoreTeam2($score) {
        $this->scoreTeam2 = $score;
        return $this;
    }

    public function getTeam1(): ?string
    {
        return $this->team1;
    }

    public function setTeam1(?string $team1): self
    {
        $this->team1 = $team1;
        return $this;

    }

    public function getTeam2(): ?string
    {
        return $this->team2;
    }

    public function setTeam2(?string $team2): self
    {
        $this->team2 = $team2;
        return $this;

    }

    public function getIsWinner(): ?int
    {
        return $this->isWinner;
    }

    public function setIsWinner(?int $isWinner): self
    {
        $this->isWinner = $isWinner;
        return $this;

    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function setSeason($season): self
    {
        $this->season = $season;
        return $this;

    }

    public function getFootMatchResults(): Collection
    {
        return $this->footMatchResults;
    }

}
