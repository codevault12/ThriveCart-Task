import { render, screen } from '@testing-library/react';
import { BasketSummary } from './BasketSummary';
import { PRODUCTS } from '../test/fakeApi';

describe('BasketSummary', () => {
  it('renders discounts, free delivery and the total', () => {
    render(
      <BasketSummary
        summary={{
          items: [PRODUCTS[0]!, PRODUCTS[0]!],
          subtotal: { cents: 6590, formatted: '$65.90' },
          discounts: [{ label: 'Second Red Widget half price', amount: { cents: 1648, formatted: '$16.48' } }],
          delivery: { cents: 0, formatted: '$0.00' },
          total: { cents: 4942, formatted: '$49.42' },
        }}
      />,
    );

    expect(screen.getByText('Second Red Widget half price')).toBeInTheDocument();
    expect(screen.getByText('−$16.48')).toBeInTheDocument();
    expect(screen.getByText('Free')).toBeInTheDocument();
    expect(screen.getByTestId('total')).toHaveTextContent('$49.42');
  });
});
