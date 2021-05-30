<?php

namespace App\Category\Entity;

use App\Shared\EntityInterface;

interface CategoryEntityInterface extends EntityInterface
{
    public function setKey(?int $key): void;

    public function getKey(): ?int;

    public function getTitle(): string;

    public function getTaxRate(): float;
}