<?php

declare(strict_types=1);

namespace App\Discount\Repository;

use App\Discount\Entity\DiscountEntity;
use App\Discount\Entity\DiscountEntityInterface;
use App\Shared\StorageAdapterInterface;

class DiscountRepository implements DiscountRepositoryInterface
{
    private StorageAdapterInterface $storage;

    public function __construct(StorageAdapterInterface $storage)
    {
        $this->storage = $storage;
    }

    public function findByBarcode(int $barcode): ?DiscountEntityInterface
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

    public function save(DiscountEntityInterface $discount): void
    {
        $data = $discount->toArray();

        if ($discount->getBarcode() === null) {
            $this->storage->insert($data);
            return;
        }

        $this->storage->update($discount->getBarcode(), $data);
    }

    /**
     * @param array<string|mixed> $rawData
     * @return DiscountEntity
     */
    private function hydrate(array $rawData): DiscountEntity
    {
        $discount = new DiscountEntity(
            $rawData['category'],
            $rawData['discount'],
        );

        $discount->setBarcode($rawData['barcode']);

        return $discount;
    }
}