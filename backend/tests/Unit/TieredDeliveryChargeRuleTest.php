<?php

declare(strict_types=1);

namespace Acme\Tests\Unit;

use Acme\Delivery\DeliveryTier;
use Acme\Delivery\TieredDeliveryChargeRule;
use Acme\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TieredDeliveryChargeRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('subtotals')]
    public function itChargesByTheTierTheSubtotalFallsIn(int $subtotal, int $expectedCharge): void
    {
        $rule = self::acmeRule();

        self::assertSame($expectedCharge, $rule->chargeFor(Money::ofCents($subtotal))->cents);
    }

    /**
     * @return iterable<string, array{int, int}>
     */
    public static function subtotals(): iterable
    {
        yield 'small order' => [100, 495];
        yield 'just under $50' => [4999, 495];
        yield 'exactly $50' => [5000, 295];
        yield 'just under $90' => [8999, 295];
        yield 'exactly $90' => [9000, 0];
        yield 'large order' => [50000, 0];
    }

    #[Test]
    public function tierOrderInConfigurationDoesNotMatter(): void
    {
        $rule = new TieredDeliveryChargeRule(
            DeliveryTier::otherwise(Money::zero()),
            DeliveryTier::below(Money::ofCents(9000), Money::ofCents(295)),
            DeliveryTier::below(Money::ofCents(5000), Money::ofCents(495)),
        );

        self::assertSame(495, $rule->chargeFor(Money::ofCents(1000))->cents);
    }

    #[Test]
    public function itRequiresExactlyOneOpenEndedTier(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TieredDeliveryChargeRule(DeliveryTier::below(Money::ofCents(5000), Money::ofCents(495)));
    }

    private static function acmeRule(): TieredDeliveryChargeRule
    {
        return new TieredDeliveryChargeRule(
            DeliveryTier::below(Money::ofCents(5000), Money::ofCents(495)),
            DeliveryTier::below(Money::ofCents(9000), Money::ofCents(295)),
            DeliveryTier::otherwise(Money::zero()),
        );
    }
}
