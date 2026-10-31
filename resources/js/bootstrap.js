import axios from 'axios';
import Auth from './Auth.js';
import { installApiAuthentication } from './apiAuthentication.js';

window.axios = axios;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
if (csrfToken) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

const disposeAuthentication = installApiAuthentication(axios, {
    auth: Auth,
    applicationUrl: import.meta.env.VITE_APP_URL || window.location.origin,
});
if (import.meta.hot) import.meta.hot.dispose(disposeAuthentication);
