# Vue 3 and Vite migration

Pluginng now uses native **Vue 3.5.44**, **Vue Router 5.4.0**, and **Vite 8.3.5**.
There is no Vue 2 compatibility runtime. Existing Options API components,
backend API routes, Bootstrap theme assets, and local-storage authentication
keys are retained. This frontend migration does not require database migrations
or new application/Passport signing keys.

## What changed

- `createApp` replaces the Vue 2 application bootstrap. Authentication stays
  reactive, and existing valid `token`/`user` local-storage values are restored.
- Routes load page components on demand. Native navigation guards check admin
  permissions even under an authenticated parent route.
- Template filters are explicit `$filters` calls. Formatting stays consistent.
- Pagination, toast notifications, loading skeletons, and idle detection use local
  Vue 3/native browser components instead of Vue 2-only packages. Unused legacy
  date-picker, map, chart, Vuex, and payment-wrapper packages are removed.
- The current Bootstrap theme remains in Blade. Dashboard theme events bind
  after Vue mounts and are removed when the layout unmounts; the old theme script
  no longer accesses elements before lazy-loaded components exist.
- Lifecycle hooks and modal transition classes use Vue 3 names. Timers and modal
  keyboard listeners are disposed on unmount.
- Firebase uses the patched modular SDK and modular update/remove calls. Project
  IDs and database configuration are retained; no Firebase data was read or
  changed during the migration. A lockfile override selects patched gRPC 1.x
  for Firebase's otherwise unused Node/Firestore dependency chain. Unused Lodash is removed from the application.
- Vite replaces Mix/Webpack. Blade uses `@vite`, generated files live in
  `public/build/`, and GitHub Actions builds with Node 24.

## Public build variables

The existing public `MIX_*` GitHub settings continue working. You do not have to
rename them before the first deployment. New `VITE_*` values take precedence:

| Preferred variable | Existing fallback |
| --- | --- |
| `VITE_APP_URL` | `MIX_APP_URL` |
| `VITE_APP_NAME` | `MIX_APP_NAME` |
| `VITE_SESSION_LIFETIME` | `MIX_SESSION_LIFETIME` |
| `VITE_RAVE_PUBLICKEY` | `MIX_RAVE_PUBLICKEY` |
| `VITE_GOOGLE_MAP_API_KEY` | `MIX_GOOGLE_MAP_API_KEY` |
| `VITE_FIREBASE_API_KEY` | `MIX_FIREBASE_API_KEY` |
| `VITE_FIREBASE_APP_ID` | `MIX_FIREBASE_APP_ID` |
| `VITE_PUSHER_APP_KEY` | `MIX_PUSHER_APP_KEY` |
| `VITE_PUSHER_APP_CLUSTER` | `MIX_PUSHER_APP_CLUSTER` |

Only the listed public legacy values are mapped into Vite. In particular,
`MIX_VUE_APP_TOKEN`, `APP_KEY`, private payment credentials, database passwords,
and Passport private keys are not mapped. Never put private values under a
`VITE_*` name either: Vite variables are browser-visible.

These settings are compiled into JavaScript. Changing GitHub variables or the
server `.env` does not change an already built bundle: rebuild and deploy after
changing public frontend settings. Server-only `MIX_MONNIFY*` and other provider
configuration still belong in the existing server `.env`.

## Local development and verification

Use Node **24.15 or later in the supported Node 24 line**. The package manifest
also lists supported Node 22/26 versions. PHP remains 8.4.

```bash
npm ci
npm run dev
```

Use the existing local Laravel application with a development database and test
provider credentials. Vite serves browser assets; Laravel continues handling
application/API routes. `npm run dev` creates `public/hot`; stop it when testing
production assets. `npm run preview` alone is not a replacement for Laravel's
history routes and backend APIs.

For a release:

```bash
npm ci
npm test
node --test tests/js/*.test.cjs
node tests/regression/palmpay.cjs
php tests/regression/palmpay.php
npm run build
php scripts/check-vite-build.php
vendor/bin/phpunit --fail-on-warning --fail-on-risky --fail-on-phpunit-notice
php artisan view:cache --env=testing
```

`npm run production` and `npm run prod` remain aliases for `npm run build`.
The old `--legacy-peer-deps` option is no longer required. The Vue suite uses
jsdom and HTTP fakes, and compiles every SFC with the native Vue 3 compiler.

## GitHub and live deployment

1. Commit the source changes, `package.json`, `package-lock.json`,
   `vite.config.mjs`, Blade templates, deployment scripts, workflows, and tests.
   Do not stage generated `public/build`, local dependencies, or `.env` files.
2. Keep the existing `CPANEL_*` settings. Keep the existing public `MIX_*`
   variables, or configure the preferred `VITE_*` names from the table above.
3. Push and wait for validation. Actions installs the locked Node dependencies,
   builds Vite assets, verifies their manifest, then runs frontend/PHP checks.
4. Rehearse on staging: existing browser login, new login/logout, navigation,
   sidebar toggle, wallet modal, BVN/NIN account creation, purchases, reports,
   pagination, transaction exports, and idle expiry. Use provider sandboxes.
5. Follow the backup/maintenance prerequisites in
   [the Laravel deployment guide](LARAVEL_13_DEPLOYMENT.md), then manually run
   **Deploy Pluginng to cPanel**. Node is needed on GitHub/build machines; it
   does not need to run on the production PHP server.
6. The workflow uploads `public/build/` along with application code. The finish
   script verifies both required entries and every manifest-listed asset,
   removes a stale `public/hot`, and then performs the existing backend checks.
   Missing assets fail deployment and leave maintenance enabled.
7. Verify the live login page references `/build/assets/...`, and check existing
   sessions, wallet UI, route navigation, and provider callbacks before resuming
   cron/queue workers. If a CDN caches HTML, refresh its affected HTML cache
   after cutover so new pages reference the current asset manifest.

The upload keeps old hashed assets, allowing open browser tabs to finish loading
chunks from the previous release. Do not remove those assets during cutover.

## Rollback

Restore the previous application release **with its matching assets and manifest**.
For the pre-Vite frontend, that includes the old Blade templates and Mix bundle;
for a Vite release, restore its corresponding `public/build/manifest.json` and
referenced assets. A frontend-only rollback does not require resetting the
production database or rotating keys. If rolling back the earlier Laravel/
Passport upgrade too, follow its separate database/client-secret recovery steps.

## Validation

Validation uses dummy data and HTTP fakes. A browser preview confirmed login,
dashboard rendering, sidebar toggling, and the wallet modal with the NIN-only
PalmPay option. No live payment, account creation, deployment, or Firebase write
was performed. Real provider and hosting behavior still requires staging.

Validated locally on **31 October 2026**:

| Check | Result |
| --- | --- |
| Native Vue 3/component suite | 28 passed; all 71 SFC scripts/templates compiled |
| Laravel/PHP suite | 28 passed, 115 assertions |
| Deployment/release checks | 7 passed |
| Existing PalmPay JS/PHP regression harnesses | Passed |
| Clean locked install (`npm ci`, without legacy peer resolution) | Passed |
| Vite production build and full manifest/asset check | Passed |
| Blade compilation and workflow YAML parsing | Passed |
| Production Node dependency audit | No advisories |
| Browser preview using simulated data | Login, dashboard, sidebar, wallet modal, and transaction navigation verified |

No code was pushed and no live server was changed during this migration.
