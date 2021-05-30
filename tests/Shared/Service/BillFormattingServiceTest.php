<?php

namespace Test\Shared\Service;

use App\Product\Entity\ProductEntity;
use App\Shared\Dto\CalculatedPriceDto;
use App\Shared\Dto\CategoryTaxAmountDto;
use App\Shared\Service\BillFormattingService;
use PHPUnit\Framework\TestCase;

class BillFormattingServiceTest extends TestCase
{
    public function testBillFormatting(): void
    {
        $service = new BillFormattingService();

        $bill = $service->format(
            [
                (new ProductEntity(1, 10, 'Product in Category 1'))->setBarcode(1),
                (new ProductEntity(1, 10, 'Product in Category 1'))->setBarcode(1),
                (new ProductEntity(2, 33, 'Product in Category 2'))->setBarcode(2),
            ],
            new CalculatedPriceDto(
                99,
                [
                    new CategoryTaxAmountDto('Category 1', 10),
                    new CategoryTaxAmountDto('Category 2', 11),
                ]
            )
        );
        $date = date('d-m-Y H:i:s');
        self::assertEquals(
            $bill,
            <<<HEREDOC
Purchase date: $date
2 x Product in Category 1: 20 €
1 x Product in Category 2: 33 €
------------------------------
Taxes:
Category 1: 10.00 €
Category 2: 11.00 €

HEREDOC

        );
    }
}
