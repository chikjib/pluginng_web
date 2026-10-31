import { defineComponent, h } from 'vue';

export const VclTable = defineComponent({
    name: 'LoadingTable',
    props: { row: { type: [Number, String], default: 5 }, column: { type: [Number, String], default: 6 } },
    setup(props) {
        return () => h('div', { class: 'loading-skeleton', role: 'status', 'aria-label': 'Loading', 'aria-busy': 'true' },
            Array.from({ length: Math.min(20, Math.max(1, Number(props.row) || 5)) }, (_, row) =>
                h('div', { key: row, class: 'loading-skeleton-row' },
                    Array.from({ length: Math.min(20, Math.max(1, Number(props.column) || 6)) }, (_, column) =>
                        h('span', { key: column, class: 'loading-skeleton-cell', 'aria-hidden': 'true' })))));
    },
});

export const VclTwitch = defineComponent({
    name: 'LoadingDashboard',
    setup() { return () => h(VclTable, { row: 5, column: 3 }); },
});
