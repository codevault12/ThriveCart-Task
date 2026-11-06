import type { Product } from '../api/types';

const SWATCHES: Record<string, string> = {
  R01: 'var(--red)',
  G01: 'var(--green)',
  B01: 'var(--blue)',
};

interface Props {
  product: Product;
  onAdd: (code: string) => void;
}

export function ProductCard({ product, onAdd }: Props) {
  return (
    <article className="product-card">
      <div className="swatch" style={{ background: SWATCHES[product.code] ?? 'var(--muted)' }} aria-hidden />
      <div className="product-info">
        <h3>{product.name}</h3>
        <span className="code">{product.code}</span>
      </div>
      <span className="price">{product.price.formatted}</span>
      <button type="button" onClick={() => onAdd(product.code)} aria-label={`Add ${product.name}`}>
        Add
      </button>
    </article>
  );
}
