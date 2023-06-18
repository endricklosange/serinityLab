<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $reservation_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $reservation_end = null;

    #[ORM\Column]
    private ?bool $status = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Order $orderService = null;

   


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getReservationStart(): ?\DateTimeInterface
    {
        return $this->reservation_start;
    }

    public function setReservationStart(\DateTimeInterface $reservation_start): self
    {
        $this->reservation_start = $reservation_start;

        return $this;
    }

    public function getReservationEnd(): ?\DateTimeInterface
    {
        return $this->reservation_end;
    }

    public function setReservationEnd(\DateTimeInterface $reservation_end): self
    {
        $this->reservation_end = $reservation_end;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->created_at = new \DateTimeImmutable();
        
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updated_at = new \DateTimeImmutable();

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getOrderService(): ?Order
    {
        return $this->orderService;
    }

    public function setOrderService(Order $orderService): self
    {
        // set the owning side of the relation if necessary
        if ($orderService->getReservation() !== $this) {
            $orderService->setReservation($this);
        }

        $this->orderService = $orderService;

        return $this;
    }
}
