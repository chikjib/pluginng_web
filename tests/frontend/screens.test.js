import { describe, it, expect, vi } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
import axios from 'axios';
import Login from '../../resources/js/components/login.vue';
import Dashboard from '../../resources/js/components/dashboard.vue';
import { formatters } from '../../resources/js/formatters.js';

function loginScreen() {
    return mount(Login, { global: {
        mocks: { axios, $route: { query: {} } },
        stubs: { RouterLink: { template: '<a><slot /></a>' } },
    } });
}

describe('migrated login screen', () => {
    it('validates required credentials before posting', async () => {
        const post = vi.spyOn(axios, 'post');
        const wrapper = loginScreen();
        await wrapper.get('form').trigger('submit');
        expect(wrapper.text()).toContain('Email cannot be empty');
        expect(post).not.toHaveBeenCalled();
        await wrapper.get('input[type="email"]').setValue('test@example.test');
        await wrapper.get('form').trigger('submit');
        expect(wrapper.text()).toContain('Password cannot be empty');
        expect(post).not.toHaveBeenCalled();
    });
    it('toggles password visibility without submitting the form', async () => {
        const post = vi.spyOn(axios, 'post');
        const wrapper = loginScreen();
        await wrapper.get('button').trigger('click');
        expect(wrapper.find('input[type="password"]').exists()).toBe(false);
        expect(post).not.toHaveBeenCalled();
    });
    it('shows API errors and unlocks the submit button for retry', async () => {
        vi.spyOn(axios, 'post').mockRejectedValue({ response: { data: { message: 'Invalid Email/Password' } } });
        const wrapper = loginScreen();
        await wrapper.get('input[type="email"]').setValue('test@example.test');
        await wrapper.get('input[type="password"]').setValue('wrong-password');
        await wrapper.get('form').trigger('submit');
        await flushPromises();
        expect(wrapper.text()).toContain('Invalid Email/Password');
        expect(wrapper.get('input[type="submit"]').element.disabled).toBe(false);
    });
});

function dashboardScreen(identity) {
    const user = { id: 1, role: 0, userlevel: 0, wallet: 1234.5, ...identity };
    const home = { balance: 1234.5, totalCommission: 0, levelPackage: '[]', notice: '', footer: '', ...identity };
    const get = vi.spyOn(axios, 'get').mockImplementation(url => Promise.resolve({ data: { success: true, data: url === '/api/load-home' ? home : user } }));
    const show = vi.fn();
    localStorage.setItem('token', 'test-token');
    const wrapper = mount(Dashboard, { global: {
        mocks: { auth: { user }, $filters: formatters, $toasted: { show }, $appUrl: 'http://localhost' },
        stubs: { Transactions: true, DashboardMenu: true, InfoCaller: true },
    } });
    return { wrapper, get, show };
}

describe('migrated PalmPay dashboard', () => {
    it.each([
        ['BVN', { bvn: 'saved-bvn', nin: null }],
        ['NIN', { bvn: null, nin: 'saved-nin' }],
        ['both', { bvn: 'saved-bvn', nin: 'saved-nin' }],
    ])('lets a user with %s request account creation and recover from a provider failure', async (_, identity) => {
        vi.spyOn(console, 'error').mockImplementation(() => {});
        const { wrapper, get, show } = dashboardScreen(identity);
        await flushPromises();
        expect(wrapper.text()).toContain('1,234.50');
        await wrapper.get('input[value="vaccount"]').setValue();
        const bank = wrapper.findAll('.bank').find(bank => bank.text().includes('PalmPay'));
        expect(bank).toBeDefined();
        get.mockRejectedValueOnce({ response: { data: { message: 'PalmPay unavailable. Please retry.' } } });
        await bank.get('button').trigger('click');
        await flushPromises();
        expect(get).toHaveBeenCalledWith('/api/reserve/account/palmpay', expect.any(Object));
        expect(show).toHaveBeenCalledWith('PalmPay unavailable. Please retry.');
        expect(wrapper.vm.isCreatingReserve_palmpay).toBe(false);
        expect(bank.get('button').text()).toContain('Create Account');
    });
    it('guides a user without an identifier to verification without calling the provider', async () => {
        const { wrapper, get } = dashboardScreen({ bvn: null, nin: null });
        await flushPromises();
        await wrapper.get('input[value="vaccount"]').setValue();
        const bank = wrapper.findAll('.bank').find(bank => bank.text().includes('PalmPay'));
        await bank.get('button').trigger('click');
        expect(wrapper.text()).toContain('A verified BVN or NIN is required');
        expect(get.mock.calls.some(([url]) => url === '/api/reserve/account/palmpay')).toBe(false);
    });
});

import Transactions from '../../resources/js/components/data/transactions.vue';
import Pagination from '../../resources/js/components/ui/Pagination.vue';

it('renders transaction amounts and loads the next Laravel resource page', async () => {
    const record = { id: 1, ref: 'TEST-TRANSACTION', category: { title: 'Data' }, description: 'Sandbox purchase', subtotal: 1000, bal: 1234.5, prev_bal: 2234.5, phone: null, response: null, status: '1', created_at: '31/10/2026' };
    const get = vi.spyOn(axios, 'get').mockImplementation(url => Promise.resolve({ data: {
        data: [record], meta: { current_page: url.includes('page=2') ? 2 : 1, last_page: 2 },
    } }));
    const wrapper = mount(Transactions, { global: {
        mocks: { auth: { user: { id: 1 } }, $filters: formatters, $toasted: { show: vi.fn() } },
        components: { pagination: Pagination },
        stubs: { RouterLink: { template: '<a><slot /></a>' } },
    } });
    await flushPromises();
    expect(wrapper.text()).toContain('1,000.00');
    expect(wrapper.text()).toContain('Confirmed');
    await wrapper.get('[aria-label="Next page"]').trigger('click');
    await flushPromises();
    expect(get).toHaveBeenCalledWith('/api/orders?page=2&user=1', expect.any(Object));
});
