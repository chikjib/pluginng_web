import { defineComponent, h, onMounted, onBeforeUnmount, watch } from 'vue';

export default defineComponent({
    name: 'IdleTimer',
    props: { duration: { type: Number, default: 300 }, wait: { type: Number, default: 5 } },
    emits: ['idle'],
    setup(props, { emit }) {
        const events = ['pointerdown', 'pointermove', 'keydown', 'scroll', 'touchstart'];
        let timer;
        const reset = () => {
            clearTimeout(timer);
            timer = setTimeout(() => emit('idle'), Math.max(1, props.duration) * 1000);
        };
        onMounted(() => {
            events.forEach(event => window.addEventListener(event, reset, { passive: true }));
            reset();
        });
        watch(() => props.duration, reset);
        onBeforeUnmount(() => {
            clearTimeout(timer);
            events.forEach(event => window.removeEventListener(event, reset));
        });
        return () => h('span', { hidden: true, 'aria-hidden': 'true' });
    },
});
