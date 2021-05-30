<?php

namespace App\Discount\Repository;

use App\Discount\Entity\DiscountEntityInterface;
use App\Shared\RepositoryInterface;

interface DiscountRepositoryInterface extends RepositoryInterface
{
    public function save(DiscountEntityInterface $discount): void;

    public function findByBarcode(int $barcode): ?DiscountEntityInterface;
}