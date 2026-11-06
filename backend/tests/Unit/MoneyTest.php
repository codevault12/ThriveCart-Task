<?php

declare(strict_types=1);

namespace Acme\Tests\Unit;

use Acme\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    #[Test]
    public function itAddsSubtractsAndMultiplies(): void
    {
        $money = Money::ofCents(1000)->add(Money::ofCents(250))->subtract(Money::ofCents(50))->multiply(3);

        self::assertSame(3600, $money->cents);
    }

    #[Test]
    public function percentageRoundsHalfUpToTheNearestCent(): void
    {
        self::assertSame(1648, Money::ofCents(3295)->percentage(50)->cents);
        self::assertSame(1647, Money::ofCents(3294)->percentage(50)->cents);
    }

    #[Test]
    public function percentageRejectsOutOfRangeValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Money::ofCents(100)->percentage(101);
    }

    #[Test]
    public function itComparesAmounts(): void
    {
        self::assertTrue(Money::ofCents(4999)->isLessThan(Money::ofCents(5000)));
        self::assertFalse(Money::ofCents(5000)->isLessThan(Money::ofCents(5000)));
        self::assertTrue(Money::ofCents(5000)->equals(Money::ofCents(5000)));
    }

    #[Test]
    #[DataProvider('formats')]
    public function itFormatsAsDollars(int $cents, string $expected): void
    {
        self::assertSame($expected, Money::ofCents($cents)->format());
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function formats(): iterable
    {
        yield 'zero' => [0, '$0.00'];
        yield 'cents only' => [5, '$0.05'];
        yield 'dollars and cents' => [9827, '$98.27'];
        yield 'negative' => [-1648, '-$16.48'];
    }

    #[Test]
    public function itSerialisesCentsAndFormattedValue(): void
    {
        self::assertSame(['cents' => 3785, 'formatted' => '$37.85'], Money::ofCents(3785)->jsonSerialize());
    }
}
