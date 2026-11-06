<?php

declare(strict_types=1);

namespace Acme\Tests\Unit;

use Acme\Catalogue\Product;
use Acme\Money;
use Acme\Offer\BuyOneGetOneDiscounted;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BuyOneGetOneDiscountedTest extends TestCase
{
    #[Test]
    #[DataProvider('redWidgetCounts')]
    public function itDiscountsEveryCompletePair(int $reds, int $expectedDiscount): void
    {
        $offer = new BuyOneGetOneDiscounted('R01', 50, 'Red BOGOHP');
        $items = [...array_fill(0, $reds, self::red()), self::green()];

        self::assertSame($expectedDiscount, $offer->discountFor($items)->cents);
    }

    /**
     * @return iterable<string, array{int, int}>
     */
    public static function redWidgetCounts(): iterable
    {
        yield 'none' => [0, 0];
        yield 'one' => [1, 0];
        yield 'one pair' => [2, 1648];
        yield 'pair plus one' => [3, 1648];
        yield 'two pairs' => [4, 3296];
    }

    #[Test]
    public function itIgnoresOtherProducts(): void
    {
        $offer = new BuyOneGetOneDiscounted('R01', 50, 'Red BOGOHP');

        self::assertSame(0, $offer->discountFor([self::green(), self::green()])->cents);
    }

    #[Test]
    public function itRejectsInvalidPercentages(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new BuyOneGetOneDiscounted('R01', 0, 'Nothing off');
    }

    private static function red(): Product
    {
        return new Product('R01', 'Red Widget', Money::ofCents(3295));
    }

    private static function green(): Product
    {
        return new Product('G01', 'Green Widget', Money::ofCents(2495));
    }
}
