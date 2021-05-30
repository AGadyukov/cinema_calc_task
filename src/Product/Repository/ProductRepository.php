<?php

declare(strict_types=1);

namespace App\Product\Repository;

use App\Product\Entity\ProductEntity;
use App\Product\Entity\ProductEntityInterface;
use App\Shared\StorageAdapterInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private StorageAdapterInterface $storage;

    public function __construct(StorageAdapterInterface $storage)
    {
        $this->storage = $storage;
    }

    public function findByBarcode(int $barcode): ?ProductEntity
    {
        $rawData = $this->storage->select(
            [
                'barcode' => $barcode,
            ]
        );

        if (empty($rawData)) {
            return null;
        }

        return $this->hydrate($rawData[0]);
    }

    public function save(ProductEntityInterface $product): void
    {
        $data = $product->toArray();

        if ($product->getBarcode() === null) {
            $this->storage->insert($data);
            return;
        }

        $this->storage->update($product->getBarcode(), $data);
    }

    /**
     * @param array<string|mixed> $rawData
     * @return ProductEntity
     */
    private function hydrate(array $rawData): ProductEntity
    {
        $product = new ProductEntity(
            $rawData['category'],
            $rawData['netPrice'],
            $rawData['description']
        );

        $product->setBarcode($rawData['barcode']);

        return $product;
    }
}