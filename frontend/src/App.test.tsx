import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { App } from './App';
import { createFakeApi } from './test/fakeApi';

describe('App', () => {
  it('lists products and prices the basket through the API as items are added and removed', async () => {
    const api = createFakeApi();
    const user = userEvent.setup();
    render(<App api={api} />);

    await user.click(await screen.findByRole('button', { name: 'Add Red Widget' }));
    await user.click(screen.getByRole('button', { name: 'Add Red Widget' }));
    await user.click(screen.getByRole('button', { name: 'Add Blue Widget' }));

    await waitFor(() => expect(screen.getByTestId('total')).toHaveTextContent('$74.85'));
    expect(screen.getByText('2 ×')).toBeInTheDocument();
    expect(api.calls.at(-1)).toEqual(['R01', 'R01', 'B01']);

    await user.click(screen.getByRole('button', { name: 'Remove one Red Widget' }));
    await waitFor(() => expect(screen.getByTestId('total')).toHaveTextContent('$41.90'));

    await user.click(screen.getByRole('button', { name: 'Clear' }));
    expect(await screen.findByText('Your basket is empty.')).toBeInTheDocument();
  });

  it('loads an example basket in one click', async () => {
    const api = createFakeApi();
    const user = userEvent.setup();
    render(<App api={api} />);

    await user.click(screen.getByRole('button', { name: /^R01, R01\s*expect/ }));

    await waitFor(() => expect(api.calls.at(-1)).toEqual(['R01', 'R01']));
  });

  it('shows API errors', async () => {
    const api = createFakeApi();
    api.priceBasket = async () => {
      throw new Error('Pricing is down');
    };
    render(<App api={api} />);

    expect(await screen.findByRole('alert')).toHaveTextContent('Pricing is down');
  });
});
