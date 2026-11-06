<?php

declare(strict_types=1);

namespace Acme\Catalogue;

use Acme\Money;
use JsonSerializable;

final readonly class Product implements JsonSerializable
{
    public function __construct(
        public string $code,
        public string $name,
        public Money $price,
    ) {}

    /**
     * @return array{code: string, name: string, price: Money}
     */
    public function jsonSerialize(): array
    {
        return ['code' => $this->code, 'name' => $this->name, 'price' => $this->price];
    }
}
