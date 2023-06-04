<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Filter
{
 
    #[Assert\Type("array")]
    private array $categories = [];

    #[Assert\Type("integer")]
    private ?int $max = null;

    #[Assert\Type("integer")]
    private ?int $min = null;

    #[Assert\Type("integer")]
    private ?int $ray = null;
    #[Assert\Type("string")]
    private ?string $places = null;

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(?array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(?int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getRay(): ?int
    {
        return $this->ray;
    }

    public function setRay(?int $ray): self
    {
        $this->ray = $ray;

        return $this;
    }

   
    public function getPlaces(): ?string
    {
        return $this->places;
    }

    public function setPlaces(?string $places)
    {
        $this->places = $places;

        return $this;
    }
}
