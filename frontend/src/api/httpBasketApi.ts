import type { BasketApi, BasketSummary, Product } from './types';

async function request<T>(url: string, init: RequestInit = {}): Promise<T> {
  const response = await fetch(url, {
    ...init,
    headers: { 'Content-Type': 'application/json', ...init.headers },
  });

  const body: unknown = await response.json().catch(() => null);
  if (!response.ok) {
    const message =
      body && typeof body === 'object' && 'error' in body && typeof body.error === 'string'
        ? body.error
        : `Request failed with status ${response.status}`;
    throw new Error(message);
  }

  return body as T;
}

export function createHttpBasketApi(baseUrl = '/api'): BasketApi {
  return {
    fetchProducts: (signal) => request<Product[]>(`${baseUrl}/products`, { signal }),
    priceBasket: (items, signal) =>
      request<BasketSummary>(`${baseUrl}/basket`, {
        method: 'POST',
        body: JSON.stringify({ items }),
        signal,
      }),
  };
}
