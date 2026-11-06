<?php

declare(strict_types=1);

namespace Acme\Tests\Unit;

use Acme\Basket\Basket;
use Acme\Catalogue\InMemoryProductCatalogue;
use Acme\Catalogue\Product;
use Acme\Catalogue\UnknownProductException;
use Acme\Delivery\DeliveryChargeRule;
use Acme\Money;
use Acme\Offer\Offer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BasketTest extends TestCase
{
    #[Test]
    public function anEmptyBasketCostsNothing(): void
    {
        $basket = new Basket(self::catalogue(), self::flatDelivery(500));

        self::assertSame(0, $basket->total()->cents);
    }

    #[Test]
    public function itSumsItemPricesAndAddsDelivery(): void
    {
        $basket = new Basket(self::catalogue(), self::flatDelivery(500));
        $basket->add('A');
        $basket->add('B');

        self::assertSame(1000 + 250 + 500, $basket->total()->cents);
    }

    #[Test]
    public function itAppliesEveryOfferAndPricesDeliveryOnTheDiscountedSubtotal(): void
    {
        $delivery = new class implements DeliveryChargeRule {
            public ?Money $seenSubtotal = null;

            public function chargeFor(Money $subtotal): Money
            {
                $this->seenSubtotal = $subtotal;

                return Money::ofCents(100);
            }
        };

        $basket = new Basket(self::catalogue(), $delivery, [self::fixedOffer('Ten off', 10), self::fixedOffer('Five off', 5)]);
        $basket->add('A');

        $summary = $basket->summary();

        self::assertSame(1000, $summary->subtotal->cents);
        self::assertCount(2, $summary->discounts);
        self::assertSame(985, $delivery->seenSubtotal?->cents);
        self::assertSame(985 + 100, $summary->total->cents);
    }

    #[Test]
    public function offersThatGrantNothingAreLeftOffTheSummary(): void
    {
        $basket = new Basket(self::catalogue(), self::flatDelivery(0), [self::fixedOffer('Nothing', 0)]);
        $basket->add('A');

        self::assertSame([], $basket->summary()->discounts);
    }

    #[Test]
    public function addingAnUnknownProductFails(): void
    {
        $basket = new Basket(self::catalogue(), self::flatDelivery(0));

        $this->expectException(UnknownProductException::class);

        $basket->add('NOPE');
    }

    private static function catalogue(): InMemoryProductCatalogue
    {
        return new InMemoryProductCatalogue(
            new Product('A', 'Product A', Money::ofCents(1000)),
            new Product('B', 'Product B', Money::ofCents(250)),
        );
    }

    private static function flatDelivery(int $cents): DeliveryChargeRule
    {
        return new readonly class (Money::ofCents($cents)) implements DeliveryChargeRule {
            public function __construct(private Money $charge) {}

            public function chargeFor(Money $subtotal): Money
            {
                return $this->charge;
            }
        };
    }

    private static function fixedOffer(string $label, int $cents): Offer
    {
        return new readonly class ($label, Money::ofCents($cents)) implements Offer {
            public function __construct(private string $label, private Money $discount) {}

            public function label(): string
            {
                return $this->label;
            }

            public function discountFor(array $items): Money
            {
                return $this->discount;
            }
        };
    }
}
