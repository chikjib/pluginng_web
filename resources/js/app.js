import './bootstrap.js';
import { createApp } from 'vue';
import axios from 'axios';
import App from './app.vue';
import router from './routes.js';
import Auth from './Auth.js';
import Pagination from './components/ui/Pagination.vue';
import IdleTimer from './components/ui/IdleTimer.js';
import { VclTable, VclTwitch } from './components/ui/LoadingSkeletons.js';
import { formatters } from './formatters.js';
import { createToasts } from './plugins/toasts.js';

const app = createApp(App);
Object.assign(app.config.globalProperties, {
    auth: Auth,
    axios,
    $axios: axios,
    $filters: formatters,
    $toasted: createToasts(),
    $appUrl: (import.meta.env.VITE_APP_URL || window.location.origin).replace(/\/$/, ''),
});
app.component('pagination', Pagination);
app.component('v-idle', IdleTimer);
app.component('vcl-table', VclTable);
app.component('vcl-twitch', VclTwitch);
app.use(router);
router.isReady().then(() => app.mount('#app'));
