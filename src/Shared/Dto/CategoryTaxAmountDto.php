<?php

declare(strict_types=1);

namespace App\Shared\Dto;

class CategoryTaxAmountDto
{
    private const TAX_AMOUNT_PRECISION = 2;

    private string $categoryTitle;
    private float $taxAmount;

    public function __construct(string $categoryTitle, float $taxAmount)
    {
        $this->categoryTitle = $categoryTitle;
        $this->taxAmount = $taxAmount;
    }

    public function getCategoryTitle(): string
    {
        return $this->categoryTitle;
    }

    public function getTaxAmount(): string
    {
        return number_format($this->taxAmount, self::TAX_AMOUNT_PRECISION);
    }
}