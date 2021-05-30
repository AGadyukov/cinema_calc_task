<?php

declare(strict_types=1);

namespace App\Product\Entity;

final class ProductEntity implements ProductEntityInterface
{
    private ?int $barcode;
    private string $description;
    private float $netPrice;
    private int $category;

    public function __construct(
        int $category,
        float $netPrice,
        string $description
    ) {
        $this->netPrice = $netPrice;
        $this->category = $category;
        $this->description = $description;
    }

    public function setBarcode(?int $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getBarcode(): ?int
    {
        return $this->barcode;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getNetPrice(): float
    {
        return $this->netPrice;
    }

    public function getCategory(): int
    {
        return $this->category;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'barcode' => $this->barcode,
            'description' => $this->description,
            'netPrice' => $this->netPrice,
            'category' => $this->category,
        ];
    }
}