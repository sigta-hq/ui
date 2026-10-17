import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

// The month a calendar opens on without a value is today's; the examples pin
// today so they render the same on every day (and in every time zone).
const today = '2026-07-21';

export default defineComponent({
    name: 'calendar',
    title: 'Calendar',
    tier: 'components',
    page: 'calendar',
    svelte: 'Calendar',
    description: 'Standalone Mon-first month grid: a mono month caption between chevrons, weekday initials and 42 square day buttons (six weeks, spilling into the months around). Reactive month navigation, full keyboard support, min/max bounds. The shared engine behind the date-picker. Emits calendar-change.',
    aria: 'WAI-ARIA date grid: role=grid labelled by the month caption (aria-live), columnheaders with the full weekday names, each day a gridcell button with its full date as aria-label, aria-selected and aria-current="date" for today; one roving tab stop.',
    props: {
        name: { type: 'string', default: null, description: 'Form name of the hidden value input.' },
        value: {
            type: 'date',
            default: null,
            bindable: true,
            description: 'The selected day, YYYY-MM-DD; the calendar opens on its month.',
            blade: { accepts: 'string|\\DateTimeInterface (Carbon)', note: 'wire:model lands on the hidden input. A Carbon/DateTimeInterface is read as its Y-m-d day.' },
        },
        min: { type: 'date', default: null, description: 'First selectable day, YYYY-MM-DD; earlier days are disabled and the month before is out of reach.' },
        max: { type: 'date', default: null, description: 'Last selectable day, YYYY-MM-DD; later days are disabled and the month after is out of reach.' },
        today: {
            type: 'date',
            default: null,
            description: 'The day marked as today, and opened on without a value, YYYY-MM-DD. Unset means the current date where it renders (the visitor\'s browser, once running). Pin it for a member\'s own time zone, or for tests and screenshots.',
        },
    },
    parts: {
        header: { description: 'The row with the month caption between the chevrons.' },
        prev: { description: 'Previous month button ("Previous month"); disabled before min.' },
        next: { description: 'Next month button ("Next month"); disabled after max.' },
        title: { description: 'The month caption, "July 2026" (labels the grid, announced on change).' },
        grid: { description: 'The role=grid; arrow keys, Home/End and PageUp/PageDown move the focus.' },
        weekdays: { description: 'The row of weekday column headers.' },
        weekday: { description: 'One column header: Mo … Su, with the full name as aria-label.' },
        week: { description: 'One row of seven days.' },
        day: { description: 'A day button (role=gridcell), data-date="YYYY-MM-DD".' },
    },
    states: {
        selected: { attribute: 'aria-selected', values: ['true', 'false'], description: 'On each day: the selected one is inked.' },
        today: { attribute: 'aria-current', values: ['date'], description: 'On today\'s day: a soft ink ring.' },
        disabled: { attribute: 'disabled', description: 'On days outside min/max, and on prev/next when that month is out of reach: faded, no clicks.' },
    },
    events: {
        pick: {
            description: 'A day was picked; bubbles from the calendar (the date-picker listens, internal).',
            blade: 'calendar-change',
            svelte: false,
            detail: '{ value: string | null, label: string }',
        },
        change: {
            description: 'The selected day changed, YYYY-MM-DD.',
            blade: 'input / change on the hidden input (wire:model hears it)',
            svelte: 'onchange',
            detail: 'string | null',
        },
    },
    uses: ['icon'],
    usage: {
        livewire: '<sigta:calendar name="day" wire:model.live="day" :value="$day" min="2026-07-06" max="2026-07-24" />',
        svelte: '<Calendar name="day" bind:value={day} min="2026-07-06" max="2026-07-24" />',
    },
    notes: {
        shared: [
            'Keyboard: arrows move a day / a week, Home/End jump to the week\'s ends, PageUp/PageDown change month, Enter or Space picks. Only the active day is tabbable (roving tabindex); a day out of range takes no focus.',
            'Days spill over from the months around (42 cells, always six weeks); picking one shows its month.',
            'Labels are English (month names, Mo … Su).',
        ],
        livewire: [
            'wire:model and every attribute except class and id land on the hidden value input, which hears input and change when a day is picked. Pass the value back through :value so a change from the server (a reset) shows.',
            'Picking a day also dispatches calendar-change (bubbling) with { value, label }: the ISO date and "9 Jul 2026".',
        ],
        svelte: [
            'bind:value (YYYY-MM-DD) and onchange(value); name and other attributes land on the hidden input for plain form posts. A value set from outside shows its month.',
            'Picking a day also dispatches a bubbling calendar-change DOM event with { value, label }: the ISO date and "9 Jul 2026".',
        ],
    },
    examples: [
        { label: 'Standalone', tree: [c('calendar', { name: 'day', value: '2026-07-09' })] },
        { label: 'No selection (opens on today)', tree: [c('calendar', { name: 'day2', today })] },
        { label: 'Bounded — min/max disable out-of-range days', tree: [c('calendar', { name: 'day3', value: '2026-07-15', min: '2026-07-06', max: '2026-07-24' })] },
        {
            label: 'Keyboard',
            tree: [
                el('p', { class: 'font-body text-[0.95rem] text-graphite max-w-prose' },
                    'Focus a day and use arrow keys to move, ',
                    el('span', { class: 'font-mono text-[0.8rem]' }, 'Home'), '/', el('span', { class: 'font-mono text-[0.8rem]' }, 'End'),
                    ' for week ends, ',
                    el('span', { class: 'font-mono text-[0.8rem]' }, 'PageUp'), '/', el('span', { class: 'font-mono text-[0.8rem]' }, 'PageDown'),
                    ' to change month, and ',
                    el('span', { class: 'font-mono text-[0.8rem]' }, 'Enter'),
                    ' to select. Only the active day is tabbable (roving tabindex).',
                ),
                el('div', { class: 'mt-5' }, c('calendar', { name: 'day4', value: '2026-07-09' })),
            ],
            interactions: [
                { focus: '[data-part="day"][tabindex="0"]' },
                { press: ['ArrowRight', 'ArrowDown', 'End'] },
                { press: ['PageDown', 'Home'] },
                { press: 'Enter' },
            ],
        },
        {
            label: 'Next month, then a day',
            tree: [c('calendar', { name: 'day5', value: '2026-07-09' })],
            interactions: [{ click: '[data-part="next"]' }, { click: '[data-date="2026-08-12"]' }],
        },
        {
            label: 'Keys stop at the bounds',
            tree: [c('calendar', { name: 'day6', value: '2026-07-22', min: '2026-07-06', max: '2026-07-24' })],
            interactions: [{ focus: '[data-part="day"][tabindex="0"]' }, { press: ['ArrowDown', 'ArrowRight', 'ArrowRight', 'ArrowRight', 'PageDown'] }],
        },
    ],
});
