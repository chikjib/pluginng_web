# Fresh login returns API 401

A bearer token appearing in the browser does not prove PHP received it or Passport accepted it. The upgrade deployment preserves `.env` and `storage/`, including Passport keys, and does not run `passport:keys` or `passport:install`.

## 1. Check the effective Passport keys

In cPanel Terminal, change to the application directory containing `artisan`, then run:

```bash
/opt/cpanel/ea-php84/root/usr/bin/php artisan upgrade:check
```

Look for `PASS: Passport signing and verification keys match`. This compares the actual configured private and public keys, including `PASSPORT_PRIVATE_KEY` / `PASSPORT_PUBLIC_KEY` overrides. It neither changes keys nor displays them.

If this check fails, restore a verified matching pair from the pre-upgrade backup and check environment overrides. Do not run `passport:keys --force`, regenerate `APP_KEY`, or delete OAuth clients to diagnose this issue. Replacing both Passport keys invalidates existing tokens; a matching new pair should still accept freshly issued tokens. A mismatched pair can reject fresh tokens too.

After correcting environment configuration, rebuild the configuration cache using the same PHP binary:

```bash
/opt/cpanel/ea-php84/root/usr/bin/php artisan config:cache
/opt/cpanel/ea-php84/root/usr/bin/php artisan upgrade:check
```

If the host keeps old PHP workers or OPcache, restart PHP through the hosting panel/support. Test a fresh login afterward.

## 2. Check whether PHP received the token

After deploying this change, inspect a failing API request in the browser Network tab, under **Response Headers**:

- `X-Auth-Header-Present: 0`: PHP did not receive a bearer token before authentication. Check Apache/PHP header forwarding.
- `X-Auth-Header-Present: 1`: PHP received a bearer token. Check Passport keys, token/client revocation, server time, and configuration consistency between the web workers and terminal.

Only share the check results and this numeric header; never share a token or key contents.

## 3. Apply header forwarding

The deployment finish script now runs `scripts/ensure-auth-header.php`. It adds Authorization forwarding to the server's existing root `.htaccess` without replacing its cPanel PHP handler or other rules. The deployment preparation step backs up that file first. `public/.htaccess` already has forwarding.

For a manual deployment, back up the existing root `.htaccess`, then run from the application directory:

```bash
/opt/cpanel/ea-php84/root/usr/bin/php scripts/ensure-auth-header.php
```

The helper is safe to rerun. It cannot configure nginx or hosting-level proxy rules; ask the host to forward Authorization if the header remains absent.

The frontend now clears rejected credentials and returns to login once on a protected API 401. It does not retry failed requests. The redirect is error handling, not proof that the underlying server authentication issue is resolved.
