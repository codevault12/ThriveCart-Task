<?php

declare(strict_types=1);

namespace Acme\Delivery;

use Acme\Money;
use InvalidArgumentException;

final readonly class TieredDeliveryChargeRule implements DeliveryChargeRule
{
    /** @var list<DeliveryTier> */
    private array $tiers;

    public function __construct(DeliveryTier ...$tiers)
    {
        $bounded = array_values(array_filter($tiers, static fn(DeliveryTier $t): bool => $t->upTo !== null));
        $open = array_values(array_filter($tiers, static fn(DeliveryTier $t): bool => $t->upTo === null));

        if (count($open) !== 1) {
            throw new InvalidArgumentException('Exactly one open-ended ("otherwise") delivery tier is required.');
        }

        usort($bounded, static fn(DeliveryTier $a, DeliveryTier $b): int => $a->upTo?->cents <=> $b->upTo?->cents);

        $this->tiers = [...$bounded, ...$open];
    }

    public function chargeFor(Money $subtotal): Money
    {
        foreach ($this->tiers as $tier) {
            if ($tier->covers($subtotal)) {
                return $tier->charge;
            }
        }

        return Money::zero();
    }
}
