<?php

namespace App\Product\Repository;

use App\Product\Entity\ProductEntityInterface;
use App\Shared\RepositoryInterface;

interface ProductRepositoryInterface extends RepositoryInterface
{
    public function save(ProductEntityInterface $product): void;

    public function findByBarcode(int $barcode): ?ProductEntityInterface;
}