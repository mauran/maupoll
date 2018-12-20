<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PollOptionRepository")
 */
class PollOption
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Poll", inversedBy="options")
     * @ORM\JoinColumn(nullable=false)
     */
    private $poll;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PollUserOption", mappedBy="PollOptionId", orphanRemoval=true)
     */
    private $Voters;

    public function __construct()
    {
        $this->Voters = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getPoll(): ?Poll
    {
        return $this->poll;
    }

    public function setPoll(?Poll $poll): self
    {
        $this->poll = $poll;

        return $this;
    }

    public function getUsers(): ?PollUserOption
    {
        return $this->users;
    }

    public function setUsers(?PollUserOption $users): self
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection|PollUserOption[]
     */
    public function getVoters(): Collection
    {
        return $this->Voters;
    }

    public function addVoter(PollUserOption $voter): self
    {
        if (!$this->Voters->contains($voter)) {
            $this->Voters[] = $voter;
            $voter->setPollOptionId($this);
        }

        return $this;
    }

    public function removeVoter(PollUserOption $voter): self
    {
        if ($this->Voters->contains($voter)) {
            $this->Voters->removeElement($voter);
            // set the owning side to null (unless already changed)
            if ($voter->getPollOptionId() === $this) {
                $voter->setPollOptionId(null);
            }
        }

        return $this;
    }
}
