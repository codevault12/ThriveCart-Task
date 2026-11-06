import type { BasketSummary as Summary } from '../api/types';
import { BasketLines } from './BasketLines';
import { BasketSummary } from './BasketSummary';

interface Props {
  summary: Summary | null;
  error: string | null;
  loading: boolean;
  onRemove: (code: string) => void;
  onClear: () => void;
}

export function BasketPanel({ summary, error, loading, onRemove, onClear }: Props) {
  const hasItems = (summary?.items.length ?? 0) > 0;

  return (
    <section className="panel basket" aria-busy={loading}>
      <header>
        <h2>Basket</h2>
        {hasItems && (
          <button type="button" className="link" onClick={onClear}>
            Clear
          </button>
        )}
      </header>
      {error && <p className="error" role="alert">{error}</p>}
      {summary && (
        <>
          <BasketLines items={summary.items} onRemove={onRemove} />
          <BasketSummary summary={summary} />
        </>
      )}
    </section>
  );
}
