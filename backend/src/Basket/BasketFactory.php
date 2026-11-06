<?php

declare(strict_types=1);

namespace Acme\Basket;

use Acme\Catalogue\ProductCatalogue;
use Acme\Delivery\DeliveryChargeRule;
use Acme\Offer\Offer;

final readonly class BasketFactory
{
    /**
     * @param list<Offer> $offers
     */
    public function __construct(
        public ProductCatalogue $catalogue,
        private DeliveryChargeRule $deliveryChargeRule,
        private array $offers,
    ) {}

    /**
     * @param iterable<string> $productCodes
     */
    public function create(iterable $productCodes = []): Basket
    {
        $basket = new Basket($this->catalogue, $this->deliveryChargeRule, $this->offers);
        foreach ($productCodes as $code) {
            $basket->add($code);
        }

        return $basket;
    }
}
