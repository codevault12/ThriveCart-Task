import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { App } from './App';
import { createHttpBasketApi } from './api/httpBasketApi';
import './styles.css';

const root = document.getElementById('root');
if (!root) throw new Error('Root element #root not found');

createRoot(root).render(
  <StrictMode>
    <App api={createHttpBasketApi()} />
  </StrictMode>,
);
