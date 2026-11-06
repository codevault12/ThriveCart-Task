// examples from the brief
const EXAMPLES: { items: string[]; expected: string }[] = [
  { items: ['B01', 'G01'], expected: '$37.85' },
  { items: ['R01', 'R01'], expected: '$54.37' },
  { items: ['R01', 'G01'], expected: '$60.85' },
  { items: ['B01', 'B01', 'R01', 'R01', 'R01'], expected: '$98.27' },
];

interface Props {
  onLoad: (items: string[]) => void;
}

export function ExampleBaskets({ onLoad }: Props) {
  return (
    <section className="panel">
      <h2>Example baskets</h2>
      <div className="examples">
        {EXAMPLES.map(({ items, expected }) => (
          <button key={items.join()} type="button" className="example" onClick={() => onLoad(items)}>
            <span className="code">{items.join(', ')}</span>
            <span>expect {expected}</span>
          </button>
        ))}
      </div>
    </section>
  );
}
