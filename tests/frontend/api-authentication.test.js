import { describe, it, expect, vi } from 'vitest';
import axios from 'axios';
import { installApiAuthentication } from '../../resources/js/apiAuthentication.js';

function clientWithAuth(adapter) {
    const client = axios.create({ adapter });
    const navigate = vi.fn();
    const auth = { logout: vi.fn(() => localStorage.removeItem('token')) };
    const dispose = installApiAuthentication(client, { auth, navigate, origin: 'http://localhost', storage: localStorage });
    return { client, navigate, auth, dispose };
}
const denied = config => Promise.reject(new axios.AxiosError('Unauthorized', 'ERR_BAD_REQUEST', config, null, { status: 401, config, data: { message: 'Unauthenticated.' } }));

describe('shared API authentication', () => {
    it('uses the latest stored token even if a page cached an old header', async () => {
        localStorage.setItem('token', 'fresh-token');
        const adapter = vi.fn(config => Promise.resolve({ status: 200, data: {}, headers: {}, config }));
        const { client } = clientWithAuth(adapter);
        await client.get('/api/users/1278', { headers: { Authorization: 'Bearer stale-token' } });
        expect(adapter.mock.calls[0][0].headers.get('Authorization')).toBe('Bearer fresh-token');
    });
    it('clears a rejected login and redirects once when concurrent protected requests return 401', async () => {
        localStorage.setItem('token', 'rejected-token');
        const { client, auth, navigate } = clientWithAuth(denied);
        const results = await Promise.allSettled([client.get('/api/load-home'), client.get('/api/users/1278')]);
        expect(results.every(result => result.status === 'rejected')).toBe(true);
        expect(auth.logout).toHaveBeenCalledTimes(1);
        expect(navigate).toHaveBeenCalledExactlyOnceWith('/login?session=unverified');
    });
    it('does not redirect or attach an old token on the public login endpoint', async () => {
        localStorage.setItem('token', 'old-token');
        const adapter = vi.fn(denied);
        const { client, auth } = clientWithAuth(adapter);
        await expect(client.post('/api/login', {}, { headers: { Authorization: 'Bearer old-token' } })).rejects.toBeDefined();
        expect(adapter.mock.calls[0][0].headers.get('Authorization')).toBeUndefined();
        expect(auth.logout).not.toHaveBeenCalled();
    });
    it('does not clear a newer login when an older in-flight request is rejected', async () => {
        localStorage.setItem('token', 'old-token');
        let reject;
        const adapter = vi.fn(config => new Promise((resolve, failure) => { reject = () => failure(new axios.AxiosError('Unauthorized', '', config, null, { status: 401, config })); }));
        const { client, auth } = clientWithAuth(adapter);
        const request = client.get('/api/load-home');
        const result = request.catch(error => error);
        await vi.waitFor(() => expect(adapter).toHaveBeenCalled());
        localStorage.setItem('token', 'new-token');
        reject();
        await result;
        expect(auth.logout).not.toHaveBeenCalled();
        expect(localStorage.getItem('token')).toBe('new-token');
    });
    it('does not log out for permission errors or third-party failures', async () => {
        localStorage.setItem('token', 'token');
        const { client, auth } = clientWithAuth(config => Promise.reject(new axios.AxiosError('Forbidden', '', config, null, { status: config.url.startsWith('https:') ? 401 : 403, config })));
        await expect(client.get('/api/admin/settings')).rejects.toBeDefined();
        await expect(client.get('https://provider.example.test/api/status')).rejects.toBeDefined();
        expect(auth.logout).not.toHaveBeenCalled();
    });
});
