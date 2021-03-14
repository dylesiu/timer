<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    const STATE_RUNNING = 'running';
    const STATE_PAUSE = 'pause';
    const STATE_ENDED = 'ended';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity=Pause::class, mappedBy="task", orphanRemoval=true)
     */
    private $pauses;

    public function __construct()
    {
        $this->setStart(new \DateTime());
        $this->setState(self::STATE_RUNNING);
        $this->pauses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function stop(): void
    {
        $endDateTime = new \DateTime();
        $this->setEnd($endDateTime);

        $diff = $endDateTime->getTimestamp() - $this->getStart()->getTimestamp();
        $diff -= $this->countBreakTime();

        $this->setTime($diff);
        $this->setState(self::STATE_ENDED);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'start' => $this->getStart()->getTimestamp(),
            'end' => $this->getEnd() ? $this->getEnd()->getTimestamp() : null,
            'state' => $this->getState(),
            'time' => $this->getTime(),
            'amountBreaks' => $this->countPauses()
        ];
    }

    /**
     * @return Collection|Pause[]
     */
    public function getPauses(): Collection
    {
        return $this->pauses;
    }

    public function countPauses(): int
    {
        return count($this->getPauses());
    }

    public function addPause(Pause $pause): self
    {
        if (!$this->pauses->contains($pause)) {
            $this->pauses[] = $pause;
            $pause->setTask($this);
        }

        return $this;
    }

    public function removePause(Pause $pause): self
    {
        if ($this->pauses->removeElement($pause)) {
            // set the owning side to null (unless already changed)
            if ($pause->getTask() === $this) {
                $pause->setTask(null);
            }
        }

        return $this;
    }

    public function countBreakTime()
    {
        $breakTime = 0;

        foreach ($this->getPauses() as $pause) {
            if ($pause->getTime()) {
                $breakTime += $pause->getTime();
            } else {
                $breakTime += (new \DateTime())->getTimestamp() - $pause->getStart()->getTimestamp();
            }
        }

        return $breakTime;
    }
}
