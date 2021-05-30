<?php

declare(strict_types=1);

namespace App\Shared\Dto;

class CalculatedPriceDto
{
    private const TOTAL_PRICE_PRECISION = 2;

    private float $totalPrice;

    /**
     * @var CategoryTaxAmountDto[]
     */
    private array $categoryTax;

    /**
     * @param float $totalPrice
     * @param CategoryTaxAmountDto[] $categoryTax
     */
    public function __construct(float $totalPrice, array $categoryTax)
    {
        $this->totalPrice = $totalPrice;
        $this->categoryTax = $categoryTax;
    }

    public function getTotalPrice(): string
    {
        return number_format($this->totalPrice, self::TOTAL_PRICE_PRECISION);
    }

    /**
     * @return CategoryTaxAmountDto[]
     */
    public function getCategoryTax(): array
    {
        return $this->categoryTax;
    }
}