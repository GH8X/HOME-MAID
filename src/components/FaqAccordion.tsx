/**
 * FAQ accordion.
 *
 * The answer panel animates with a CSS grid row transition, so it stays in the
 * DOM (crawlable and screen-readable) rather than being unmounted. One answer
 * is open at a time within a list; clicking an open question closes it.
 */

import { useId, useState } from 'react';

import type { Faq } from '../lib/types';
import { classNames } from '../lib/format';
import { Icon } from './Icon';

interface FaqItemShape {
  question: string;
  answer: string;
}

function AccordionRow({
  item,
  index,
  openIndex,
  onToggle,
  idBase,
}: {
  item: FaqItemShape;
  index: number;
  openIndex: number | null;
  onToggle: (index: number) => void;
  idBase: string;
}) {
  const isOpen = openIndex === index;
  const buttonId = `${idBase}-q-${index}`;
  const panelId = `${idBase}-a-${index}`;

  return (
    <div className={classNames('faq-item', isOpen && 'is-open')}>
      <h3 style={{ margin: 0 }}>
        <button
          type="button"
          id={buttonId}
          className="faq-item__button"
          aria-expanded={isOpen}
          aria-controls={panelId}
          onClick={() => onToggle(index)}
        >
          <span>{item.question}</span>
          <span className="faq-item__icon" aria-hidden="true">
            <Icon name={isOpen ? 'minus' : 'plus'} size={15} />
          </span>
        </button>
      </h3>
      <div className="faq-item__answer" id={panelId} role="region" aria-labelledby={buttonId}>
        <div className="faq-item__answer-inner">
          <div>{item.answer}</div>
        </div>
      </div>
    </div>
  );
}

export function FaqAccordion({
  items,
  idPrefix = 'faq',
}: {
  items: FaqItemShape[];
  idPrefix?: string;
}) {
  const generated = useId().replace(/[:]/g, '');
  const [openIndex, setOpenIndex] = useState<number | null>(0);
  const base = `${idPrefix}-${generated}`;

  return (
    <div className="faq-items">
      {items.map((item, index) => (
        <AccordionRow
          key={`${item.question}-${index}`}
          item={item}
          index={index}
          openIndex={openIndex}
          onToggle={(next) => setOpenIndex((current) => (current === next ? null : next))}
          idBase={base}
        />
      ))}
    </div>
  );
}

/** Same accordion, grouped under category headings. */
export function FaqGroups({
  groups,
  idPrefix = 'faq-group',
}: {
  groups: Array<{ category: string; items: FaqItemShape[] }>;
  idPrefix?: string;
}) {
  return (
    <div className="faq-list">
      {groups.map((group, groupIndex) => (
        <section className="faq-group" key={group.category}>
          <h2 className="faq-group__title">
            {group.category}
            <span className="faq-group__count">{group.items.length}</span>
          </h2>
          <FaqAccordion items={group.items} idPrefix={`${idPrefix}-${groupIndex}`} />
        </section>
      ))}
    </div>
  );
}

export function groupFaqs(items: Faq[]): Array<{ category: string; items: Faq[] }> {
  const groups: Array<{ category: string; items: Faq[] }> = [];
  for (const item of items) {
    const existing = groups.find((group) => group.category === item.category);
    if (existing) existing.items.push(item);
    else groups.push({ category: item.category, items: [item] });
  }
  return groups;
}
