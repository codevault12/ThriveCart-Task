import type { BasketSummary as Summary } from '../api/types';

interface Props {
  summary: Summary;
}

export function BasketSummary({ summary }: Props) {
  return (
    <dl className="summary">
      <div>
        <dt>Subtotal</dt>
        <dd>{summary.subtotal.formatted}</dd>
      </div>
      {summary.discounts.map((discount) => (
        <div key={discount.label} className="discount">
          <dt>{discount.label}</dt>
          <dd>−{discount.amount.formatted}</dd>
        </div>
      ))}
      <div>
        <dt>Delivery</dt>
        <dd>{summary.delivery.cents === 0 && summary.items.length > 0 ? 'Free' : summary.delivery.formatted}</dd>
      </div>
      <div className="total">
        <dt>Total</dt>
        <dd data-testid="total">{summary.total.formatted}</dd>
      </div>
    </dl>
  );
}
