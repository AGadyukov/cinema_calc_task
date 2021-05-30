<?php

namespace Test\Shared\Service;

use App\Category\Entity\CategoryEntity;
use App\Category\Repository\CategoryRepositoryInterface;
use App\Discount\Entity\DiscountEntity;
use App\Product\Entity\ProductEntity;
use App\Shared\Service\PriceCalculatorService;
use PHPUnit\Framework\TestCase;

class PriceCalculatorServiceTest extends TestCase
{
    /**
     * @dataProvider priceProvider
     */
    public function testTotalPriceCalculation(float $netPrice, float $tax, float $discountAmount, string $expected): void
    {
        $repository = $this->createMock(CategoryRepositoryInterface::class);
        $categoryEntity =  new CategoryEntity('mocked category', $tax);
        $categoryEntity->setKey(
            random_int(1, 9999)
        );
        $repository->expects(self::once())->method('findByKey')->willReturn($categoryEntity);
        $repository->expects(self::never())->method('save');

        $service = new PriceCalculatorService($repository);
        $calculatedDto = $service->calculateTotalPrice(
            [
                new ProductEntity($categoryEntity->getKey(), $netPrice, 'mocked product')
            ],
            [
                new DiscountEntity($categoryEntity->getKey(), $discountAmount)
            ]
        );

        self::assertEquals(
            $expected,
            $calculatedDto->getTotalPrice(),
        );

        self::assertEquals(
            'mocked category',
            $calculatedDto->getCategoryTax()[0]->getCategoryTitle()
        );

        self::assertEquals(
            ($netPrice - ($netPrice * ($discountAmount / 100))) * ($tax / 100),
            $calculatedDto->getCategoryTax()[0]->getTaxAmount(),
            'Asserts that summed taxes per product category equal to the formula result'
        );
    }

    public function priceProvider(): iterable
    {
        yield 'No tax, only discount' => [
            100,
            0,
            10,
            '90.00'
        ];

        yield 'Only tax, no discount' => [
            100,
            10,
            0,
            '110.00'
        ];

        yield 'Tax and discount' => [
            100,
            10,
            10,
            '99.00'
        ];
    }
}
