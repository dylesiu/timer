<?php

namespace App\Entity;

use App\Repository\PauseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PauseRepository::class)
 */
class Pause
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="pauses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    public function __construct()
    {
        $this->setStart(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): \DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function stop(): void
    {
        $endDateTime = new \DateTime();
        $this->setEnd($endDateTime);
        $diff = $endDateTime->getTimestamp() - $this->getStart()->getTimestamp();
        $this->setTime($diff);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'start' => $this->getStart()->getTimestamp(),
            'end' => $this->getEnd() ? $this->getEnd()->getTimestamp() : null,
            'time' => $this->getTime(),
            'taskId' => $this->getTask()->getId()
        ];
    }
}
