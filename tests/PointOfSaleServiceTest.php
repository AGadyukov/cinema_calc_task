<?php

namespace Test;

use App\Category\Entity\CategoryEntity;
use App\Category\Repository\CategoryRepositoryInterface;
use App\Discount\Entity\DiscountEntity;
use App\Discount\Repository\DiscountRepositoryInterface;
use App\PointOfSaleService;
use App\Product\Entity\ProductEntity;
use App\Product\Repository\ProductRepositoryInterface;
use App\Shared\Service\BillFormattingService;
use App\Shared\Service\PriceCalculatorService;
use PHPUnit\Framework\TestCase;

class PointOfSaleServiceTest extends TestCase
{
    public function testScanProducts(): void
    {
        $category = new CategoryEntity('Tickets', 10);
        $category->setKey(1);
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $categoryRepository->method('findByKey')->willReturn($category);

        $product = new ProductEntity(
            $category->getKey(),
            50,
            'Movie ticket'
        );
        $product->setBarcode(random_int(1, 999));
        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        $productRepository->method('findByBarcode')->willReturnCallback(
            fn(int $barcode) => $barcode === $product->getBarcode() ? $product : null
        );

        $discount = new DiscountEntity(
            $category->getKey(),
            10
        );
        $discount->setBarcode(random_int(1, 999));
        $discountRepository = $this->createMock(DiscountRepositoryInterface::class);
        $discountRepository->method('findByBarcode')->willReturn($discount);


        $service = new PointOfSaleService(
            $productRepository,
            $discountRepository,
            new PriceCalculatorService($categoryRepository),
            new BillFormattingService()
        );

        $service->scanBarcode($product->getBarcode());
        $service->scanBarcode($discount->getBarcode());

        $scannedProducts = (fn() => $this->scannedProducts)->bindTo($service, PointOfSaleService::class)();
        $scannedDiscounts = (fn() => $this->scannedDiscounts)->bindTo($service, PointOfSaleService::class)();

        self::assertCount(1, $scannedProducts);
        self::assertEquals(
            $product,
            $scannedProducts[0]
        );
        self::assertCount(1, $scannedDiscounts);
        self::assertEquals(
            $discount,
            $scannedDiscounts[$product->getCategory()]
        );

        self::assertEquals(
            '49.50',
            $service->getTotalPrice()
        );

        $date = date('d-m-Y H:i:s');
        self::assertEquals(
            <<<HEREDOC
Purchase date: $date
1 x Movie ticket: 50 €
------------------------------
Taxes:
Tickets: 4.50 €

HEREDOC,
            $service->getBill()
        );


        $service->reset();
        $scannedProducts = (fn() => $this->scannedProducts)->bindTo($service, PointOfSaleService::class)();
        $scannedDiscounts = (fn() => $this->scannedDiscounts)->bindTo($service, PointOfSaleService::class)();

        self::assertEmpty($scannedProducts);
        self::assertEmpty($scannedDiscounts);
    }
}
