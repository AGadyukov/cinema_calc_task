<?php

namespace App;

use App\Discount\Entity\DiscountEntityInterface;
use App\Discount\Repository\DiscountRepositoryInterface;
use App\Shared\Dto\CalculatedPriceDto;
use App\Shared\Service\PriceCalculatorService;
use App\Product\Entity\ProductEntityInterface;
use App\Product\Repository\ProductRepositoryInterface;
use App\Shared\Service\BillFormattingService;

/**
 * Entry point (the Facade pattern) that encapsulates barcodes scanning, total price calculation and bill formatting.
 */
class PointOfSaleService
{
    /**
     * @var ProductEntityInterface[]
     */
    private array $scannedProducts = [];

    /**
     * @var DiscountEntityInterface[]
     */
    private array $scannedDiscounts = [];

    private ?CalculatedPriceDto $calculatedPriceDto = null;

    private ProductRepositoryInterface $productRepository;
    private DiscountRepositoryInterface $discountRepository;
    private PriceCalculatorService $calculatorService;
    private BillFormattingService $billFormattingService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        DiscountRepositoryInterface $discountRepository,
        PriceCalculatorService $calculatorService,
        BillFormattingService $billPrinterService
    ) {
        $this->productRepository = $productRepository;
        $this->discountRepository = $discountRepository;
        $this->calculatorService = $calculatorService;
        $this->billFormattingService = $billPrinterService;
    }

    public function scanBarcode(int $barcode): bool
    {
        $product = $this->productRepository->findByBarcode($barcode);
        if ($product !== null) {
            $this->scannedProducts[] = $product;
            return true;
        }

        $discount = $this->discountRepository->findByBarcode($barcode);
        if ($discount !== null && !isset($this->scannedDiscounts[$discount->getCategory()])) {
            $this->scannedDiscounts[$discount->getCategory()] = $discount;
            return true;
        }

        return false;
    }

    public function reset(): void
    {
        $this->scannedProducts = [];
        $this->scannedDiscounts = [];
        $this->calculatedPriceDto = null;
    }

    public function getTotalPrice(): string
    {
        $this->calculatedPriceDto = $this->calculatorService->calculateTotalPrice(
            $this->scannedProducts,
            $this->scannedDiscounts
        );

        return $this->calculatedPriceDto->getTotalPrice();
    }

    public function getBill(): string
    {
        $calculatedPriceDto = $this->calculatedPriceDto;
        if ($calculatedPriceDto === null) {
            $this->getTotalPrice();

            /** @var CalculatedPriceDto $calculatedPriceDto */
            $calculatedPriceDto = $this->calculatedPriceDto;
        }

        return $this->billFormattingService->format(
            $this->scannedProducts,
            $calculatedPriceDto
        );
    }
}