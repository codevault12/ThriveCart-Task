<?php

declare(strict_types=1);

namespace Acme\Offer;

use Acme\Catalogue\Product;
use Acme\Money;
use InvalidArgumentException;

// Discount applies once per pair: 3 items => 1 discount, 4 => 2
final readonly class BuyOneGetOneDiscounted implements Offer
{
    public function __construct(
        private string $productCode,
        private int $percentOff,
        private string $label,
    ) {
        if ($percentOff < 1 || $percentOff > 100) {
            throw new InvalidArgumentException("Percent off must be between 1 and 100, got {$percentOff}.");
        }
    }

    public function label(): string
    {
        return $this->label;
    }

    public function discountFor(array $items): Money
    {
        $matching = array_values(array_filter(
            $items,
            fn(Product $product): bool => $product->code === $this->productCode,
        ));

        if ($matching === []) {
            return Money::zero();
        }

        $pairs = intdiv(count($matching), 2);

        return $matching[0]->price->percentage($this->percentOff)->multiply($pairs);
    }
}
