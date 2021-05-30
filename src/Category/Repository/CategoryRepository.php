<?php

declare(strict_types=1);

namespace App\Category\Repository;

use App\Category\Entity\CategoryEntity;
use App\Category\Entity\CategoryEntityInterface;
use App\Shared\StorageAdapterInterface;

final class CategoryRepository implements CategoryRepositoryInterface
{
    private StorageAdapterInterface $storage;

    public function __construct(StorageAdapterInterface $storage)
    {
        $this->storage = $storage;
    }

    public function findByKey(int $key): ?CategoryEntityInterface
    {
        $rawData = $this->storage->select(
            [
                'barcode' => $key,
            ]
        );

        if (empty($rawData)) {
            return null;
        }

        return $this->hydrate($rawData[0]);
    }

    public function save(CategoryEntityInterface $category): void
    {
        $data = $category->toArray();

        if ($category->getKey() === null) {
            $this->storage->insert($data);
            return;
        }

        $this->storage->update($category->getKey(), $data);
    }

    /**
     * @param array<string|mixed> $rawData
     * @return CategoryEntity
     */
    private function hydrate(array $rawData): CategoryEntity
    {
        $category = new CategoryEntity(
            $rawData['title'],
            $rawData['taxRate']
        );

        $category->setKey($rawData['key']);

        return $category;
    }
}