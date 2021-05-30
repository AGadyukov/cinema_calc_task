<?php

declare(strict_types=1);

namespace App\Category\Entity;

class CategoryEntity implements CategoryEntityInterface
{
    private ?int $key = null;
    private string $title;
    private float $taxRate;

    public function __construct(string $title, float $taxRate)
    {
        $this->title = $title;
        $this->taxRate = $taxRate;
    }

    public function getKey(): ?int
    {
        return $this->key;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function setKey(?int $key): void
    {
        $this->key = $key;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'title' => $this->title,
            'taxRate' => $this->taxRate,
        ];
    }
}