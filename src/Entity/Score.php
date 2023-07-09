<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $price_quality = null;

    #[ORM\Column]
    private ?int $cleanliness = null;

    #[ORM\Column]
    private ?int $location = null;

    #[ORM\Column]
    private ?int $product = null;

    #[ORM\OneToOne(mappedBy: 'score', targetEntity: Review::class)]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPriceQuality(): ?int
    {
        return $this->price_quality;
    }

    public function setPriceQuality(int $price_quality): self
    {
        $this->price_quality = $price_quality;

        return $this;
    }

    public function getCleanliness(): ?int
    {
        return $this->cleanliness;
    }

    public function setCleanliness(int $cleanliness): self
    {
        $this->cleanliness = $cleanliness;

        return $this;
    }

    public function getLocation(): ?int
    {
        return $this->location;
    }

    public function setLocation(int $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getProduct(): ?int
    {
        return $this->product;
    }

    public function setProduct(int $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setScore($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getScore() === $this) {
                $review->setScore(null);
            }
        }

        return $this;
    }
}
