// Resolve the token when a request is sent, rather than when a lazy page was imported.
export function installApiAuthentication(client, { auth, storage = window.localStorage, origin = window.location.origin, applicationUrl = origin, navigate = url => window.location.replace(url) }) {
    const origins = new Set([new URL(origin).origin, new URL(applicationUrl || origin, origin).origin]);
    let redirecting = false;
    const protectedApi = config => {
        if (!config?.url) return false;
        const url = new URL(config.url, config.baseURL || origin);
        return origins.has(url.origin) && url.pathname.startsWith('/api/')
            && !['/api/login', '/api/register'].includes(url.pathname)
            && !url.pathname.startsWith('/api/auth/');
    };
    const getHeader = config => config.headers?.get?.('Authorization') ?? config.headers?.Authorization;
    const request = client.interceptors.request.use(config => {
        const url = new URL(config.url || '/', config.baseURL || origin);
        if (origins.has(url.origin) && (url.pathname === '/api/login' || url.pathname === '/api/register' || url.pathname.startsWith('/api/auth/'))) {
            if (config.headers?.delete) config.headers.delete('Authorization');
            else delete config.headers.Authorization;
        } else if (protectedApi(config)) {
            const token = storage.getItem('token');
            if (token) config.headers.Authorization = `Bearer ${token}`;
            else if (config.headers?.delete) config.headers.delete('Authorization');
            else delete config.headers.Authorization;
        }
        return config;
    });
    const response = client.interceptors.response.use(value => value, error => {
        if (error.response?.status === 401 && protectedApi(error.config)) {
            const current = storage.getItem('token');
            const sent = getHeader(error.config);
            // An old in-flight response must not clear a newer login.
            if (!redirecting && (sent === (current ? `Bearer ${current}` : undefined) || (!sent && !current))) {
                redirecting = true;
                auth.logout();
                navigate('/login?session=unverified');
            }
        }
        return Promise.reject(error);
    });
    return () => {
        client.interceptors.request.eject(request);
        client.interceptors.response.eject(response);
    };
}
