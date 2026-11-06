import { useEffect, useState } from 'react';
import type { BasketApi, Product } from '../api/types';

export function useProducts(api: BasketApi) {
  const [products, setProducts] = useState<Product[]>([]);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const controller = new AbortController();
    api
      .fetchProducts(controller.signal)
      .then(setProducts)
      .catch((e: unknown) => {
        if (!controller.signal.aborted) setError(e instanceof Error ? e.message : String(e));
      });
    return () => controller.abort();
  }, [api]);

  return { products, error };
}
