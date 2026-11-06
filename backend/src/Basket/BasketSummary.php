<?php

declare(strict_types=1);

namespace Acme\Basket;

use Acme\Catalogue\Product;
use Acme\Money;
use JsonSerializable;

final readonly class BasketSummary implements JsonSerializable
{
    /**
     * @param list<Product>         $items
     * @param list<AppliedDiscount> $discounts
     */
    public function __construct(
        public array $items,
        public Money $subtotal,
        public array $discounts,
        public Money $delivery,
        public Money $total,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'discounts' => $this->discounts,
            'delivery' => $this->delivery,
            'total' => $this->total,
        ];
    }
}
