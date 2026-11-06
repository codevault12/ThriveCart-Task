<?php

declare(strict_types=1);

namespace Acme\Tests\Acceptance;

use Acme\Basket\BasketFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExampleBasketsTest extends TestCase
{
    /**
     * @param list<string> $codes
     */
    #[Test]
    #[DataProvider('examples')]
    public function itPricesTheExampleBaskets(array $codes, string $expectedTotal): void
    {
        /** @var BasketFactory $factory */
        $factory = require __DIR__ . '/../../config/acme.php';

        $basket = $factory->create();
        foreach ($codes as $code) {
            $basket->add($code);
        }

        self::assertSame($expectedTotal, $basket->total()->format());
    }

    /**
     * @return iterable<string, array{list<string>, string}>
     */
    public static function examples(): iterable
    {
        yield 'B01, G01' => [['B01', 'G01'], '$37.85'];
        yield 'R01, R01' => [['R01', 'R01'], '$54.37'];
        yield 'R01, G01' => [['R01', 'G01'], '$60.85'];
        yield 'B01, B01, R01, R01, R01' => [['B01', 'B01', 'R01', 'R01', 'R01'], '$98.27'];
    }
}
