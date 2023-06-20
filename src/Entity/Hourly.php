<?php

namespace App\Entity;

use App\Repository\HourlyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HourlyRepository::class)]
class Hourly
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $openingTime = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $closureHour = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $breakTime = null;

    #[ORM\OneToMany(mappedBy: 'hourly_id', targetEntity: Activity::class)]
    private Collection $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOpeningTime(): ?\DateTimeInterface
    {
        return $this->openingTime;
    }

    public function setOpeningTime(\DateTimeInterface $openingTime): self
    {
        $this->openingTime = $openingTime;

        return $this;
    }

    public function getClosureHour(): ?\DateTimeInterface
    {
        return $this->closureHour;
    }

    public function setClosureHour(\DateTimeInterface $closureHour): self
    {
        $this->closureHour = $closureHour;

        return $this;
    }

    public function getBreakTime(): ?\DateTimeInterface
    {
        return $this->breakTime;
    }

    public function setBreakTime(\DateTimeInterface $breakTime): self
    {
        $this->breakTime = $breakTime;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setHourlyId($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getHourlyId() === $this) {
                $activity->setHourlyId(null);
            }
        }

        return $this;
    }
}
