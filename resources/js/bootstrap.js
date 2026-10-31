import axios from 'axios';

window.axios = axios;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
if (csrfToken) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
