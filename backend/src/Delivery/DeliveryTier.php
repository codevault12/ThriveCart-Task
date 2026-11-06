<?php

declare(strict_types=1);

namespace Acme\Delivery;

use Acme\Money;

// $upTo is exclusive; null = no upper limit
final readonly class DeliveryTier
{
    public function __construct(
        public ?Money $upTo,
        public Money $charge,
    ) {}

    public static function below(Money $upTo, Money $charge): self
    {
        return new self($upTo, $charge);
    }

    public static function otherwise(Money $charge): self
    {
        return new self(null, $charge);
    }

    public function covers(Money $subtotal): bool
    {
        return $this->upTo === null || $subtotal->isLessThan($this->upTo);
    }
}
