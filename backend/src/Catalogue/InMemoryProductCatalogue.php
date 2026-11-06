<?php

declare(strict_types=1);

namespace Acme\Catalogue;

use InvalidArgumentException;

final readonly class InMemoryProductCatalogue implements ProductCatalogue
{
    /** @var array<string, Product> */
    private array $products;

    public function __construct(Product ...$products)
    {
        $indexed = [];
        foreach ($products as $product) {
            if (isset($indexed[$product->code])) {
                throw new InvalidArgumentException("Duplicate product code '{$product->code}'.");
            }
            $indexed[$product->code] = $product;
        }
        $this->products = $indexed;
    }

    public function get(string $code): Product
    {
        return $this->products[$code] ?? throw UnknownProductException::forCode($code);
    }

    public function all(): array
    {
        return array_values($this->products);
    }
}
