# Pluginng Laravel 13 upgrade validation

Validated locally on **22 October 2026** using **PHP 8.4.20**.
The resolved stable framework is **Laravel 13.38.0**, with Passport 13.10.0,
Sanctum 4.3.3, Carbon 3.15.0, and PHPUnit 12.5.40. The Composer platform is
PHP 8.4.0, so deployments use the same PHP minor version. `composer.lock` is
part of the release.

## Results

| Check | Result |
| --- | --- |
| PHP feature/unit suite | 28 tests, 115 assertions; no warnings, risky tests, or PHPUnit notices |
| Deployment script tests | 5 passed |
| PalmPay browser regression harness | Passed |
| PalmPay isolated backend regression harness | Passed |
| PHP syntax | All application, configuration, routes, migrations, and test sources passed |
| Composer manifest | `composer validate --strict` passed |
| PHP runtime extensions | `composer check-platform-reqs` passed |
| Locked dependency audit | No security advisories or abandoned packages |
| Registered application/OAuth routes | 213 |
| Blade compilation | Passed with testing configuration |
| Laravel Mix production assets | Built successfully |
| Workflow YAML and shell syntax | Passed |
| Production-only dependency installation | Clean temporary release booted Laravel 13.38.0 without development dependencies; runtime requirements passed |
| First-commit file preview | Environment credentials, OAuth keys, uploads, sessions, caches, logs, and local dependency directories excluded |

## What the tests exercise

- Existing PHP-serialized file sessions survive multiple requests.
- Existing RS256 API tokens work until revoked.
- New login and logout preserve other devices' tokens.
- Incorrect passwords cannot revoke another device's token; unknown users fail cleanly.
- Existing UUID OAuth clients can issue password-grant tokens and refresh them.
- Client-secret hashing preserves client IDs and is safe to repeat.
- The readiness command rejects unconverted secrets, then passes with the required state.
- Existing session cookie/cache/Redis namespaces remain explicit.
- Runtime payment metadata remains available when configuration is cached.
- PalmPay creation accepts BVN, NIN, or both, with the appropriate provider type.
- Missing/invalid identifiers and failed/malformed provider responses cannot save a broken account.
- Existing PalmPay reserved accounts are reused without another provider call.
- Valid webhook signatures pass; tampered payloads and missing/incorrect signatures fail.
- PalmPay Base64 signatures preserve literal `+` and percent-encoded characters.
- Signed PalmPay deposits retain the percentage/capped fee calculation and are not
  credited again on sequential redelivery of the same provider reference.
- Decimal wallet debit/credit balances and date-filtered transaction CSV exports work.
- Deployment ordering performs migrations, secret conversion, readiness checks,
  and caches before reopening. A failed migration/readiness check keeps maintenance active.

## Boundaries

PHP tests use in-memory SQLite databases, disposable RSA keys, dummy environment
values, and HTTP fakes. Deployment tests use temporary files and a fake PHP CLI.
No live database, account creation, payment, SMS, email, or production deployment
was performed. These tests do not establish concurrency guarantees for payment
handlers. Existing queued jobs, MySQL behavior, the PHP web handler, filesystem
ownership, OPcache, and external providers must be rehearsed on isolated staging.

Production deployment is deliberately manual in GitHub Actions. The local
folder had no Git repository/remote; no code was pushed and no GitHub secrets
were changed. Follow [the deployment guide](LARAVEL_13_DEPLOYMENT.md) for initial
repository setup, hosting configuration, cutover, and rollback.

## Frontend follow-up — 31 October 2026

The frontend has since migrated from Vue 2 / Mix to native Vue 3 / Vite.
See [the frontend migration guide](VUE3_VITE_DEPLOYMENT.md) for current build,
variable compatibility, testing, and deployment instructions.
