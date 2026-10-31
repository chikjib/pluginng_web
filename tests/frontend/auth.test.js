import { describe, it, expect } from 'vitest';
import axios from 'axios';
import { AuthSession } from '../../resources/js/Auth.js';
import { guardNavigation } from '../../resources/js/routing/guard.js';

const route = (...meta) => ({ fullPath: '/dashboard/users', matched: meta.map(meta => ({ meta })) });

describe('Vue 3 authentication and navigation', () => {
    it('restores existing tokens and user details without changing storage keys', () => {
        localStorage.setItem('token', 'existing-token');
        localStorage.setItem('user', JSON.stringify({ id: 7, role: 0 }));
        const auth = new AuthSession();
        expect(auth.check()).toBe(true);
        expect(auth.user.id).toBe(7);
        expect(axios.defaults.headers.common.Authorization).toBe('Bearer existing-token');
    });
    it('recovers from invalid persisted user JSON without crashing startup', () => {
        localStorage.setItem('token', 'existing-token');
        localStorage.setItem('user', '{invalid');
        const auth = new AuthSession();
        expect(auth.check()).toBe(false);
        expect(localStorage.getItem('token')).toBeNull();
        expect(axios.defaults.headers.common.Authorization).toBeUndefined();
    });
    it('clears authentication and headers on logout while preserving unrelated storage', () => {
        const auth = new AuthSession();
        localStorage.setItem('unrelated', 'keep');
        auth.login('new-token', { id: 1, remember_token: 'remember' });
        expect(auth.check()).toBe(true);
        auth.logout();
        expect(auth.check()).toBe(false);
        expect(auth.token).toBeNull();
        expect(axios.defaults.headers.common.Authorization).toBeUndefined();
        expect(localStorage.getItem('unrelated')).toBe('keep');
    });
    it('redirects guests on protected and admin routes', () => {
        const guest = { check: () => false, user: null };
        expect(guardNavigation(route({ requiresAuth: true }), guest).path).toBe('/login');
        expect(guardNavigation(route({ requiresAdmin: true }), guest).path).toBe('/login');
    });
    it('checks admin permissions even beneath an authenticated parent', () => {
        const protectedAdmin = route({ requiresAuth: true }, { requiresAdmin: true });
        expect(guardNavigation(protectedAdmin, { check: () => true, user: { role: 0 } })).toEqual({ path: '/dashboard' });
        expect(guardNavigation(protectedAdmin, { check: () => true, user: { role: 1 } })).toBe(true);
    });
    it('allows public routes', () => {
        expect(guardNavigation(route({}), { check: () => false, user: null })).toBe(true);
    });
});
