import type { BasketApi } from './api/types';
import { BasketPanel } from './components/BasketPanel';
import { ExampleBaskets } from './components/ExampleBaskets';
import { ProductGrid } from './components/ProductGrid';
import { useBasket } from './hooks/useBasket';
import { useProducts } from './hooks/useProducts';

interface Props {
  api: BasketApi;
}

export function App({ api }: Props) {
  const { products, error: productsError } = useProducts(api);
  const basket = useBasket(api);

  return (
    <div className="app">
      <header className="masthead">
        <h1>Acme Widget Co</h1>
        <p>Buy one Red Widget, get the second half price · Free delivery on orders of $90 or more</p>
      </header>
      <main className="layout">
        <div className="stack">
          {productsError ? (
            <section className="panel">
              <p className="error" role="alert">Could not load products: {productsError}</p>
            </section>
          ) : (
            <ProductGrid products={products} onAdd={basket.add} />
          )}
          <ExampleBaskets onLoad={basket.replace} />
        </div>
        <BasketPanel
          summary={basket.summary}
          error={basket.error}
          loading={basket.loading}
          onRemove={basket.remove}
          onClear={basket.clear}
        />
      </main>
    </div>
  );
}
