<?php

namespace App\Category\Repository;

use App\Category\Entity\CategoryEntityInterface;
use App\Shared\RepositoryInterface;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function save(CategoryEntityInterface $category): void;

    public function findByKey(int $key): ?CategoryEntityInterface;
}