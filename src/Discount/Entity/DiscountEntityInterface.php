<?php

namespace App\Discount\Entity;

use App\Shared\EntityInterface;

interface DiscountEntityInterface extends EntityInterface
{
    public function getBarcode(): ?int;

    public function setBarcode(?int $barcode): void;

    public function getCategory(): int;

    public function getDiscount(): float;
}