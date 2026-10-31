import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Pagination from '../../resources/js/components/ui/Pagination.vue';
import IdleTimer from '../../resources/js/components/ui/IdleTimer.js';
import Modal from '../../resources/js/components/modal.vue';
import { VclTable } from '../../resources/js/components/ui/LoadingSkeletons.js';
import { createToasts } from '../../resources/js/plugins/toasts.js';
import { formatters } from '../../resources/js/formatters.js';

describe('native Vue 3 UI', () => {
    it('emits page changes for Laravel resource pagination without navigating the browser', async () => {
        const wrapper = mount(Pagination, { props: { data: { meta: { current_page: 1, last_page: 5 } }, align: 'center' } });
        expect(wrapper.get('[aria-label="Previous page"]').attributes('disabled')).toBeDefined();
        await wrapper.get('[aria-label="Next page"]').trigger('click');
        expect(wrapper.emitted('pagination-change-page')).toEqual([[2]]);
        await wrapper.setProps({ data: { meta: { current_page: 5, last_page: 5 } } });
        expect(wrapper.get('[aria-label="Next page"]').attributes('disabled')).toBeDefined();
    });
    it('handles plain Laravel paginator data and gaps', async () => {
        const wrapper = mount(Pagination, { props: { data: { current_page: 6, last_page: 12 }, limit: 1 } });
        expect(wrapper.text()).toContain('…');
        await wrapper.get('[aria-label="Go to page 7"]').trigger('click');
        expect(wrapper.emitted('pagination-change-page')).toEqual([[7]]);
    });
    it('hides pagination for a single page', () => {
        expect(mount(Pagination, { props: { data: { current_page: 1, last_page: 1 } } }).find('nav').exists()).toBe(false);
    });
    it('resets idle detection on activity and disposes timers on unmount', async () => {
        vi.useFakeTimers();
        const wrapper = mount(IdleTimer, { props: { duration: 10 } });
        vi.advanceTimersByTime(9000);
        window.dispatchEvent(new Event('pointermove'));
        vi.advanceTimersByTime(9000);
        expect(wrapper.emitted('idle')).toBeUndefined();
        vi.advanceTimersByTime(1000);
        expect(wrapper.emitted('idle')).toHaveLength(1);
        window.dispatchEvent(new Event('pointermove'));
        wrapper.unmount();
        expect(vi.getTimerCount()).toBe(0);
    });
    it('removes modal keyboard listeners on unmount', async () => {
        const onClose = vi.fn();
        const wrapper = mount(Modal, { props: { show: true, onClose } });
        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
        expect(onClose).toHaveBeenCalledTimes(1);
        wrapper.unmount();
        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
        expect(onClose).toHaveBeenCalledTimes(1);
    });
    it('renders an accessible loading placeholder', () => {
        const wrapper = mount(VclTable, { props: { row: 2, column: 3 } });
        expect(wrapper.get('[role="status"]').attributes('aria-busy')).toBe('true');
        expect(wrapper.findAll('.loading-skeleton-cell')).toHaveLength(6);
    });
    it('displays notification text safely and removes it after the timeout', () => {
        vi.useFakeTimers();
        createToasts().show(['<img src=x onerror=alert(1)>', 'Try again']);
        expect(document.querySelector('[role="status"]').textContent).toContain('<img');
        expect(document.querySelector('[role="status"] img')).toBeNull();
        vi.advanceTimersByTime(1000);
        expect(document.querySelector('[data-pluginng-toasts]')).toBeNull();
    });
    it('retains the existing money and date display formats', () => {
        expect(formatters.formatNumber(1234.5)).toBe('1,234.50');
        expect(formatters.formatPrice(1234)).toBe('1,234');
        expect(formatters.formatDate('2026-10-31')).toBe('31/10/2026');
        expect(formatters.formatDate(null)).toBe('');
    });
});

import { initializeDashboardTheme } from '../../resources/js/dashboardTheme.js';

it('binds sidebar controls created after the async layout and disposes the handler', () => {
    const cleanup = initializeDashboardTheme();
    const host = document.createElement('div');
    host.innerHTML = '<button id="sidebarToggleTop">Toggle</button><ul class="sidebar"></ul>';
    document.body.append(host);
    host.querySelector('button').click();
    expect(host.querySelector('.sidebar').classList.contains('toggled')).toBe(true);
    expect(document.body.classList.contains('sidebar-toggled')).toBe(true);
    cleanup();
    expect(document.body.classList.contains('sidebar-toggled')).toBe(false);
    host.querySelector('button').click();
    expect(document.body.classList.contains('sidebar-toggled')).toBe(false);
    host.remove();
});
