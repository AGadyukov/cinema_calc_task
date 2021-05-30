<?php

declare(strict_types=1);

namespace App\Discount\Entity;

class DiscountEntity implements DiscountEntityInterface
{
    private int $category;
    private float $discount;
    private ?int $barcode;

    public function __construct(int $category, float $discount)
    {
        $this->category = $category;
        $this->discount = $discount;
    }

    public function getCategory(): int
    {
        return $this->category;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getBarcode(): ?int
    {
        return $this->barcode;
    }

    public function setBarcode(?int $barcode): void
    {
        $this->barcode = $barcode;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'barcode' => $this->barcode,
            'discount' => $this->discount,
            'category' => $this->category,
        ];
    }
}