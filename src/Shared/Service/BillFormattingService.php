<?php

declare(strict_types=1);

namespace App\Shared\Service;

use App\Product\Entity\ProductEntityInterface;
use App\Shared\Dto\CalculatedPriceDto;

class BillFormattingService
{
    /**
     * @param ProductEntityInterface[] $products
     * @param CalculatedPriceDto $calculatedPriceDto
     * @return string
     */
    public function format(
        array $products,
        CalculatedPriceDto $calculatedPriceDto
    ): string {
        $bill = 'Purchase date: ' . date('d-m-Y H:i:s') . "\n";

        foreach ($this->getGroupedProductAmount($products) as $groupedProduct) {
            $bill .= count($groupedProduct) . " x {$groupedProduct[0]['description']}: " . array_sum(array_column($groupedProduct, 'price')) . ' €';
            $bill .= "\n";
        }

        $bill .= str_pad('', 30, '-');
        $bill .= "\n";
        $bill .= "Taxes:\n";

        foreach ($calculatedPriceDto->getCategoryTax() as $categoryTax) {
            $bill .= $categoryTax->getCategoryTitle() . ': ' . $categoryTax->getTaxAmount() . ' €';
            $bill .= "\n";
        }

        return $bill;
    }

    /**
     * @param ProductEntityInterface[] $products
     * @return array<int|string, array<int, array<string, float|string>>>
     */
    private function getGroupedProductAmount(array $products): array
    {
        $groupedProducts = [];
        foreach ($products as $product) {
            $groupedProducts[(int)$product->getBarcode()][] = [
                'description' => $product->getDescription(),
                'price' => $product->getNetPrice(),
            ];
        }

        return $groupedProducts;
    }
}