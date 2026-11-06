import type { Product } from '../api/types';
import { ProductCard } from './ProductCard';

interface Props {
  products: Product[];
  onAdd: (code: string) => void;
}

export function ProductGrid({ products, onAdd }: Props) {
  return (
    <section className="panel">
      <h2>Products</h2>
      <div className="product-grid">
        {products.map((product) => (
          <ProductCard key={product.code} product={product} onAdd={onAdd} />
        ))}
      </div>
    </section>
  );
}
