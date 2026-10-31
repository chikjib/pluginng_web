import { afterEach, beforeEach, vi } from 'vitest';
import { enableAutoUnmount } from '@vue/test-utils';
import axios from 'axios';

enableAutoUnmount(afterEach);
beforeEach(() => {
    localStorage.clear();
    vi.stubGlobal('axios', axios);
});
afterEach(() => {
    vi.restoreAllMocks();
    vi.unstubAllGlobals();
    vi.unstubAllEnvs();
    vi.useRealTimers();
    document.querySelectorAll('[data-pluginng-toasts]').forEach(element => element.remove());
});
