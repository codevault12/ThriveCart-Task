export interface Money {
  cents: number;
  formatted: string;
}

export interface Product {
  code: string;
  name: string;
  price: Money;
}

export interface AppliedDiscount {
  label: string;
  amount: Money;
}

export interface BasketSummary {
  items: Product[];
  subtotal: Money;
  discounts: AppliedDiscount[];
  delivery: Money;
  total: Money;
}

export interface BasketApi {
  fetchProducts(signal?: AbortSignal): Promise<Product[]>;
  priceBasket(items: string[], signal?: AbortSignal): Promise<BasketSummary>;
}
