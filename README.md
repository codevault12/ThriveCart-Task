# Acme Widget Co - Basket

PHP basket for the Acme Widget Co coding test, with a small React/TypeScript UI.

## Running it

Requires PHP 8.3+, Composer and Node 20+.

```bash
cd backend
composer install
composer test        # phpunit
composer analyse     # phpstan (level max)
php bin/basket B01 B01 R01 R01 R01   # $98.27  (-v for a breakdown)

composer serve       # API on :8000

cd ../frontend
npm install
npm run dev          # UI on :5173, proxies /api to :8000
npm test
```

## How it works

```php
$basket = new Basket($catalogue, $deliveryChargeRule, $offers);
$basket->add('R01');
$basket->total(); // Money
```

- `ProductCatalogue`, `DeliveryChargeRule` and `Offer` are interfaces, injected into `Basket`. New offers or delivery schemes are new classes - the basket doesn't change.
- The real products, delivery tiers and offers are wired up in `backend/config/acme.php`, which is shared by the CLI, the API and the acceptance tests.
- Money is stored as integer cents to avoid float rounding issues.
- Pricing order: subtotal → offer discounts → delivery (on the discounted subtotal) → total.
- The API (`backend/public/index.php`) is stateless: the UI posts the list of codes and gets a breakdown back, so all pricing logic stays in PHP.

## Assumptions

- Delivery is calculated on the subtotal **after** discounts. `R01, R01` = $65.90 - $16.48 = $49.42, which is under $50 and gets $4.95 delivery, giving the expected $54.37.
- Half price rounds the discount half up ($16.475 → $16.48). This is what makes the expected totals work.
- The red widget offer applies per pair: 3 reds = 1 discount, 4 reds = 2.
- "Under $50" / "under $90" are strict: exactly $90.00 ships free.
- An empty basket costs $0.00 (no delivery charge).
- Unknown product codes throw `UnknownProductException`.
- Single currency, no tax, stock or persistence.
