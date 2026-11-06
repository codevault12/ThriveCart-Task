<?php

declare(strict_types=1);

use Acme\Basket\BasketFactory;
use Acme\Catalogue\InMemoryProductCatalogue;
use Acme\Catalogue\Product;
use Acme\Delivery\DeliveryTier;
use Acme\Delivery\TieredDeliveryChargeRule;
use Acme\Money;
use Acme\Offer\BuyOneGetOneDiscounted;

return new BasketFactory(
    catalogue: new InMemoryProductCatalogue(
        new Product('R01', 'Red Widget', Money::ofCents(3295)),
        new Product('G01', 'Green Widget', Money::ofCents(2495)),
        new Product('B01', 'Blue Widget', Money::ofCents(795)),
    ),
    deliveryChargeRule: new TieredDeliveryChargeRule(
        DeliveryTier::below(Money::ofCents(5000), Money::ofCents(495)),
        DeliveryTier::below(Money::ofCents(9000), Money::ofCents(295)),
        DeliveryTier::otherwise(Money::zero()),
    ),
    offers: [
        new BuyOneGetOneDiscounted('R01', 50, 'Buy one Red Widget, get the second half price'),
    ],
);
