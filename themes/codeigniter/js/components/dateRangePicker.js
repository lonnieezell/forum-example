import { easepick } from '@easepick/core';
import { RangePlugin } from '@easepick/range-plugin';

export function initDateRangePicker(elem) {
  const picker = new easepick.create({
    element: elem,
    css: [
      'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
      'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
    ],
    zIndex: 10,
    plugins: [RangePlugin],
    setup(picker) {
      picker.on('select', (e) => {
        elem.dispatchEvent(new CustomEvent('customDateRangeSelected'));
      });
    },
  });
}
