<?php

declare(strict_types=1);

namespace Acme\Tests\Unit;

use Acme\Catalogue\InMemoryProductCatalogue;
use Acme\Catalogue\Product;
use Acme\Catalogue\UnknownProductException;
use Acme\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InMemoryProductCatalogueTest extends TestCase
{
    #[Test]
    public function itFindsProductsByCode(): void
    {
        $red = new Product('R01', 'Red Widget', Money::ofCents(3295));
        $catalogue = new InMemoryProductCatalogue($red);

        self::assertSame($red, $catalogue->get('R01'));
        self::assertSame([$red], $catalogue->all());
    }

    #[Test]
    public function itRejectsUnknownCodes(): void
    {
        $this->expectException(UnknownProductException::class);
        $this->expectExceptionMessage("Unknown product code 'X99'.");

        (new InMemoryProductCatalogue())->get('X99');
    }

    #[Test]
    public function itRejectsDuplicateCodes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InMemoryProductCatalogue(
            new Product('R01', 'Red Widget', Money::ofCents(3295)),
            new Product('R01', 'Other Red', Money::ofCents(100)),
        );
    }
}
