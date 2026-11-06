<?php

declare(strict_types=1);

namespace Acme\Basket;

use Acme\Catalogue\Product;
use Acme\Catalogue\ProductCatalogue;
use Acme\Catalogue\UnknownProductException;
use Acme\Delivery\DeliveryChargeRule;
use Acme\Money;
use Acme\Offer\Offer;

final class Basket
{
    /** @var list<Product> */
    private array $items = [];

    /**
     * @param list<Offer> $offers
     */
    public function __construct(
        private readonly ProductCatalogue $catalogue,
        private readonly DeliveryChargeRule $deliveryChargeRule,
        private readonly array $offers = [],
    ) {}

    /**
     * @throws UnknownProductException
     */
    public function add(string $productCode): void
    {
        $this->items[] = $this->catalogue->get($productCode);
    }

    public function total(): Money
    {
        return $this->summary()->total;
    }

    public function summary(): BasketSummary
    {
        $subtotal = array_reduce(
            $this->items,
            static fn(Money $sum, Product $product): Money => $sum->add($product->price),
            Money::zero(),
        );

        $discounts = [];
        foreach ($this->offers as $offer) {
            $amount = $offer->discountFor($this->items);
            if (!$amount->equals(Money::zero())) {
                $discounts[] = new AppliedDiscount($offer->label(), $amount);
            }
        }

        $discountTotal = array_reduce(
            $discounts,
            static fn(Money $sum, AppliedDiscount $discount): Money => $sum->add($discount->amount),
            Money::zero(),
        );

        // Delivery is based on the discounted subtotal (required by the R01, R01 example)
        $discountedSubtotal = $subtotal->subtract($discountTotal);
        $delivery = $this->items === [] ? Money::zero() : $this->deliveryChargeRule->chargeFor($discountedSubtotal);

        return new BasketSummary(
            items: $this->items,
            subtotal: $subtotal,
            discounts: $discounts,
            delivery: $delivery,
            total: $discountedSubtotal->add($delivery),
        );
    }
}
