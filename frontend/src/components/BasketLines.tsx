import type { Product } from '../api/types';

interface Props {
  items: Product[];
  onRemove: (code: string) => void;
}

interface Line {
  product: Product;
  quantity: number;
}

function toLines(items: Product[]): Line[] {
  const lines = new Map<string, Line>();
  for (const product of items) {
    const line = lines.get(product.code);
    if (line) line.quantity += 1;
    else lines.set(product.code, { product, quantity: 1 });
  }
  return [...lines.values()];
}

export function BasketLines({ items, onRemove }: Props) {
  if (items.length === 0) {
    return <p className="empty">Your basket is empty.</p>;
  }

  return (
    <ul className="lines">
      {toLines(items).map(({ product, quantity }) => (
        <li key={product.code}>
          <span className="qty">{quantity} ×</span>
          <span className="name">{product.name}</span>
          <span className="amount">{product.price.formatted} each</span>
          <button
            type="button"
            className="remove"
            onClick={() => onRemove(product.code)}
            aria-label={`Remove one ${product.name}`}
          >
            −
          </button>
        </li>
      ))}
    </ul>
  );
}
