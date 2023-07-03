<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[ORM\OneToOne(inversedBy: 'company', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'company', cascade: ['persist', 'remove'])]
    private ?Activity $activity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

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

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity): self
    {
        // set the owning side of the relation if necessary
        if ($activity->getCompany() !== $this) {
            $activity->setCompany($this);
        }

        $this->activity = $activity;

        return $this;
    }
}
