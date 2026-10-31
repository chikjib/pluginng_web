import { it, expect, vi } from 'vitest';

vi.mock('../../resources/js/firebase.js', () => ({ default: { testDatabase: true } }));
vi.mock('firebase/database', () => ({
    ref: vi.fn((database, path) => ({ database, path })),
    child: vi.fn((reference, path) => ({ reference, path })),
    set: vi.fn(), get: vi.fn(), update: vi.fn(), remove: vi.fn(),
}));
import service from '../../resources/js/services/OrderDataService.js';
import { get, set, update, remove } from 'firebase/database';

it('keeps Firebase order reads and creates on their existing paths', () => {
    service.getOrder('test-reference');
    expect(get).toHaveBeenCalledWith(expect.objectContaining({ path: 'orders/test-reference' }));
    service.create({ ref: 'test-reference', status: 'pending' });
    expect(set).toHaveBeenCalledWith(expect.objectContaining({ path: 'orders/test-reference' }), { ref: 'test-reference', status: 'pending' });
});

it('uses modular Firebase methods for existing update and removal paths', () => {
    service.update('orders/test-reference', { status: 'complete' });
    expect(update).toHaveBeenCalledWith(expect.objectContaining({ path: 'orders/test-reference' }), { status: 'complete' });
    service.delete('orders/test-reference');
    expect(remove).toHaveBeenCalledWith(expect.objectContaining({ path: 'orders/test-reference' }));
});
