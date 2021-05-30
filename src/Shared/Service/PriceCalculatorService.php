<?php

declare(strict_types=1);

namespace App\Shared\Service;

use App\Category\Entity\CategoryEntityInterface;
use App\Category\Exception\CategoryNotFoundException;
use App\Category\Repository\CategoryRepositoryInterface;
use App\Discount\Entity\DiscountEntityInterface;
use App\Shared\Dto\CalculatedPriceDto;
use App\Shared\Dto\CategoryTaxAmountDto;
use App\Product\Entity\ProductEntityInterface;

class PriceCalculatorService
{
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @var array<string, float>
     */
    private array $categoriesTaxAmount = [];

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param ProductEntityInterface[] $products
     * @param DiscountEntityInterface[] $discounts
     * @return CalculatedPriceDto
     *
     * @throws CategoryNotFoundException when product's category does not exist
     */
    public function calculateTotalPrice(array $products, array $discounts): CalculatedPriceDto
    {
        $totalPrice = 0;

        foreach ($products as $product) {
            $totalPrice += $this->calculateProductTotalPrice(
                $product,
                $this->getCategoryDiscount($product->getCategory(), $discounts)
            );
        }

        $categoryTaxAmountDtoList = [];
        foreach ($this->categoriesTaxAmount as $categoryTitle => $taxAmount) {
            $categoryTaxAmountDtoList[] = new CategoryTaxAmountDto(
                $categoryTitle,
                $taxAmount
            );
        }

        return new CalculatedPriceDto(
            $totalPrice,
            $categoryTaxAmountDtoList
        );
    }

    /**
     * @param ProductEntityInterface $product
     * @param DiscountEntityInterface|null $categoryDiscount
     * @return float
     *
     * @throws CategoryNotFoundException
     */
    private function calculateProductTotalPrice(
        ProductEntityInterface $product,
        ?DiscountEntityInterface $categoryDiscount
    ): float {
        $productPrice = $product->getNetPrice();
        $productCategory = $this->categoryRepository->findByKey($product->getCategory());

        if ($productCategory === null) {
            throw new CategoryNotFoundException();
        }

        if ($categoryDiscount !== null) {
            $productPrice -= $this->calculateDiscountAmount($productPrice, $categoryDiscount);
        }

        if (isset($this->categoriesTaxAmount[$productCategory->getTitle()]) === false) {
            $this->categoriesTaxAmount[$productCategory->getTitle()] = 0;
        }

        $taxAmount = $this->calculateTaxAmount($productPrice, $productCategory);
        $this->categoriesTaxAmount[$productCategory->getTitle()] += $taxAmount;
        $productPrice += $taxAmount;

        return $productPrice;
    }

    private function calculateDiscountAmount(float $price, DiscountEntityInterface $categoryDiscount): float
    {
        return $price * ($categoryDiscount->getDiscount() / 100);
    }

    private function calculateTaxAmount(float $price, CategoryEntityInterface $productCategory): float
    {
        return $price * ($productCategory->getTaxRate() / 100);
    }

    /**
     * @param int $category
     * @param DiscountEntityInterface[] $discounts
     * @return DiscountEntityInterface|null
     */
    private function getCategoryDiscount(int $category, array $discounts): ?DiscountEntityInterface
    {
        foreach ($discounts as $discount) {
            if ($discount->getCategory() === $category) {
                return $discount;
            }
        }
        return null;
    }
}