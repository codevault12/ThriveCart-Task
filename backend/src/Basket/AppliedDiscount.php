<?php

declare(strict_types=1);

namespace Acme\Basket;

use Acme\Money;
use JsonSerializable;

final readonly class AppliedDiscount implements JsonSerializable
{
    public function __construct(
        public string $label,
        public Money $amount,
    ) {}

    /**
     * @return array{label: string, amount: Money}
     */
    public function jsonSerialize(): array
    {
        return ['label' => $this->label, 'amount' => $this->amount];
    }
}
