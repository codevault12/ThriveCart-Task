import type { BasketApi, BasketSummary, Money, Product } from '../api/types';

const money = (cents: number): Money => ({ cents, formatted: `$${(cents / 100).toFixed(2)}` });

export const PRODUCTS: Product[] = [
  { code: 'R01', name: 'Red Widget', price: money(3295) },
  { code: 'B01', name: 'Blue Widget', price: money(795) },
];

// Simplified pricing (flat $1 delivery) - real rules are tested in PHP
export function createFakeApi(): BasketApi & { calls: string[][] } {
  const calls: string[][] = [];
  return {
    calls,
    fetchProducts: async () => PRODUCTS,
    priceBasket: async (codes): Promise<BasketSummary> => {
      calls.push(codes);
      const items = codes.map((code) => {
        const product = PRODUCTS.find((p) => p.code === code);
        if (!product) throw new Error(`Unknown product code '${code}'.`);
        return product;
      });
      const subtotal = items.reduce((sum, p) => sum + p.price.cents, 0);
      const delivery = items.length > 0 ? 100 : 0;
      return {
        items,
        subtotal: money(subtotal),
        discounts: [],
        delivery: money(delivery),
        total: money(subtotal + delivery),
      };
    },
  };
}
