<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PollRepository")
 */
class Poll
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @var string
     * @ORM\Column(name="question", type="string")
     */
    private $question;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $pollData;

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    /**
     * @return array
     */
    public function getPollData(): array
    {
        return $this->pollData;
    }

    /**
     * @param array $pollData
     */
    public function setPollData(array $pollData): void
    {
        $this->pollData = $pollData;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

}
