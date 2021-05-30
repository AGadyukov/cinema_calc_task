<?php

namespace App\Product\Entity;

use App\Shared\EntityInterface;

interface ProductEntityInterface extends EntityInterface
{
    public function getDescription(): string;

    public function getNetPrice(): float;

    public function getBarcode(): ?int;

    public function setBarcode(?int $barcode): self;

    public function getCategory(): int;
}