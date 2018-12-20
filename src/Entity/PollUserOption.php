<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PollUserOptionRepository")
 */
class PollUserOption
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $user;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PollOption", inversedBy="Voters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $PollOptionId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPollOptionId(): ?PollOption
    {
        return $this->PollOptionId;
    }

    public function setPollOptionId(?PollOption $PollOptionId): self
    {
        $this->PollOptionId = $PollOptionId;

        return $this;
    }
}
