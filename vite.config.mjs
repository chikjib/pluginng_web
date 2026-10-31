import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// Existing GitHub/cPanel MIX_* public settings continue working during cutover.
// Only these public values are mapped; private MIX_* values are never exposed.
const publicSettings = [
    'APP_URL', 'APP_NAME', 'SESSION_LIFETIME', 'RAVE_PUBLICKEY',
    'GOOGLE_MAP_API_KEY', 'FIREBASE_API_KEY', 'FIREBASE_APP_ID',
    'PUSHER_APP_KEY', 'PUSHER_APP_CLUSTER',
];

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const define = Object.fromEntries(publicSettings.map(name => [
        `import.meta.env.VITE_${name}`,
        JSON.stringify(env[`VITE_${name}`] || env[`MIX_${name}`] || ''),
    ]));
    return {
        define,
        plugins: [
            laravel({ input: ['resources/js/app.js', 'resources/sass/app.scss'], refresh: ['resources/views/**'] }),
            vue({ template: { transformAssetUrls: { base: null, includeAbsolute: false } } }),
        ],
        build: { target: ['es2020', 'safari15'], sourcemap: false },
        test: {
            environment: 'jsdom',
            include: ['tests/frontend/**/*.test.js'],
            setupFiles: ['tests/frontend/setup.js'],
            clearMocks: true,
        },
    };
});
