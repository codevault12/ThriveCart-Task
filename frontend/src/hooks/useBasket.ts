import { useCallback, useEffect, useState } from 'react';
import type { BasketApi, BasketSummary } from '../api/types';

export function useBasket(api: BasketApi) {
  const [items, setItems] = useState<string[]>([]);
  const [summary, setSummary] = useState<BasketSummary | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    // abort stale requests so an old response can't overwrite a newer one
    const controller = new AbortController();
    setLoading(true);
    api
      .priceBasket(items, controller.signal)
      .then((result) => {
        setSummary(result);
        setError(null);
      })
      .catch((e: unknown) => {
        if (!controller.signal.aborted) setError(e instanceof Error ? e.message : String(e));
      })
      .finally(() => {
        if (!controller.signal.aborted) setLoading(false);
      });
    return () => controller.abort();
  }, [api, items]);

  const add = useCallback((code: string) => setItems((current) => [...current, code]), []);

  const remove = useCallback(
    (code: string) =>
      setItems((current) => {
        const index = current.lastIndexOf(code);
        return index === -1 ? current : current.filter((_, i) => i !== index);
      }),
    [],
  );

  const replace = useCallback((codes: string[]) => setItems([...codes]), []);

  const clear = useCallback(() => setItems([]), []);

  return { items, summary, error, loading, add, remove, replace, clear };
}
