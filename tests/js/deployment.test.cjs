const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const { spawnSync } = require('node:child_process');
const root = path.resolve(__dirname, '../..');

test('deployment scripts have valid bash syntax', () => {
  for (const name of ['deploy-prepare.sh', 'deploy-finish.sh']) {
    const result = spawnSync('bash', ['-n', path.join(root, 'scripts', name)], { encoding: 'utf8' });
    assert.equal(result.status, 0, result.stderr);
  }
});

function finish(failure = '') {
  const app = fs.mkdtempSync(path.join(os.tmpdir(), 'pluginng-deploy-test-'));
  fs.mkdirSync(path.join(app, 'storage/framework'), { recursive: true });
  fs.writeFileSync(path.join(app, 'storage/framework/down'), '{}');
  const php = path.join(app, 'fake-php');
  fs.writeFileSync(php, `#!/usr/bin/env bash
printf '%s\\n' "$*" >> commands.log
if [[ "$1" == artisan && "$2" == "$FAIL_COMMAND" ]]; then exit 1; fi
if [[ "$1" == artisan && "$2" == up ]]; then rm storage/framework/down; fi
`, { mode: 0o700 });
  const result = spawnSync('bash', [path.join(root, 'scripts/deploy-finish.sh'), app, php], {
    cwd: root, encoding: 'utf8', env: { ...process.env, FAIL_COMMAND: failure },
  });
  const commands = fs.readFileSync(path.join(app, 'commands.log'), 'utf8');
  const down = fs.existsSync(path.join(app, 'storage/framework/down'));
  fs.rmSync(app, { recursive: true, force: true });
  return { result, commands, down };
}

test('finish checks authentication and caches before reopening', () => {
  const { result, commands, down } = finish();
  assert.equal(result.status, 0, result.stderr);
  const expected = ['scripts/check-vite-build.php', 'scripts/ensure-auth-header.php', 'artisan package:discover', 'artisan migrate --force', 'artisan passport:hash --force',
    'artisan upgrade:check', 'artisan config:cache', 'artisan view:cache', 'artisan queue:restart', 'artisan up'];
  let previous = -1;
  for (const command of expected) {
    const next = commands.split('\n').findIndex(line => line === command || line.startsWith(command + ' '));
    assert.ok(next > previous, `${command} must run in order`);
    previous = next;
  }
  assert.equal(down, false);
});

test('failed authentication readiness keeps maintenance mode active', () => {
  const { result, commands, down } = finish('upgrade:check');
  assert.notEqual(result.status, 0);
  assert.equal(down, true);
  assert.doesNotMatch(commands, /artisan up(?:\n|$)/);
});

test('failed migration keeps maintenance mode active without hashing clients', () => {
  const { result, commands, down } = finish('migrate');
  assert.notEqual(result.status, 0);
  assert.equal(down, true);
  assert.doesNotMatch(commands, /artisan passport:hash/);
});

test('manual deployment preserves server-owned state and verified SSH trust', () => {
  const workflow = fs.readFileSync(path.join(root, '.github/workflows/deploy.yml'), 'utf8');
  assert.match(workflow, /workflow_dispatch:/);
  assert.doesNotMatch(workflow, /\n\s+push:/);
  assert.match(workflow, /cancel-in-progress: false/);
  assert.match(workflow, /StrictHostKeyChecking=yes/);
  assert.doesNotMatch(workflow, /ssh-keyscan|StrictHostKeyChecking=accept-new/);
  for (const pattern of ['storage/', '.env*', 'public/storage', '/.htaccess', 'bootstrap/cache/*.php']) {
    assert.ok(workflow.includes(`--exclude='${pattern}'`));
  }
  assert.match(workflow, /rsync -az --delete --protect-args --delay-updates/);
});

function checkViteFixture(missingAsset = false) {
  const directory = fs.mkdtempSync(path.join(os.tmpdir(), 'pluginng-vite-test-'));
  const build = path.join(directory, 'public/build');
  fs.mkdirSync(path.join(build, 'assets'), { recursive: true });
  const manifest = {
    'resources/js/app.js': { file: 'assets/app.js' },
    'resources/sass/app.scss': { file: 'assets/app.css' },
    'resources/js/components/dashboard.vue': { file: 'assets/dashboard.js' },
  };
  fs.writeFileSync(path.join(build, 'manifest.json'), JSON.stringify(manifest));
  fs.writeFileSync(path.join(build, 'assets/app.js'), '// fixture');
  fs.writeFileSync(path.join(build, 'assets/app.css'), 'body{}');
  if (!missingAsset) fs.writeFileSync(path.join(build, 'assets/dashboard.js'), '// fixture');
  const result = spawnSync('php', [path.join(root, 'scripts/check-vite-build.php'), directory], { encoding: 'utf8' });
  fs.rmSync(directory, { recursive: true, force: true });
  return result;
}

test('Vite release check accepts a complete generated bundle', () => {
  const result = checkViteFixture();
  assert.equal(result.status, 0, result.stderr);
});

test('Vite release check rejects a missing lazy-loaded route asset', () => {
  const result = checkViteFixture(true);
  assert.notEqual(result.status, 0);
  assert.match(result.stderr, /asset is missing/);
});

test('Authorization forwarding preserves cPanel handlers and is idempotent', () => {
  const directory = fs.mkdtempSync(path.join(os.tmpdir(), 'pluginng-auth-header-'));
  const file = path.join(directory, '.htaccess');
  const original = '# cPanel managed handler\nAddHandler application/x-httpd-ea-php84 .php\n<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteRule ^ server.php [L]\n</IfModule>\n';
  fs.writeFileSync(file, original, { mode: 0o640 });
  const run = () => spawnSync('php', [path.join(root, 'scripts/ensure-auth-header.php'), directory], { encoding: 'utf8' });
  assert.equal(run().status, 0);
  const patched = fs.readFileSync(file, 'utf8');
  assert.ok(patched.includes(original));
  assert.match(patched, /E=HTTP_AUTHORIZATION:%\{HTTP:Authorization\}/);
  assert.equal(fs.statSync(file).mode & 0o777, 0o640);
  assert.equal(run().status, 0);
  assert.equal(fs.readFileSync(file, 'utf8'), patched);
  fs.rmSync(directory, { recursive: true, force: true });
});
