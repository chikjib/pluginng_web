// Run with: node tests/regression/palmpay.cjs
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const source = fs.readFileSync(path.join(__dirname, '../../resources/js/components/dashboard.vue'), 'utf8');
const method = source.slice(source.indexOf(' async reserve_account(bank)'), source.indexOf('\n\n    regenerate_account()')).trim().replace(/,$/, '');

async function run(user, response, failure) {
  const requests = [], messages = [], alerts = [];
  const window = { location: { href: '' } };
  const context = { latestUser: user, token: 'test-token', $toasted: { show: message => messages.push(message) } };
  const sandbox = {
    axios: { get: async url => { requests.push(url); if (failure) throw failure; return { data: response }; } },
    window, alert: message => alerts.push(message),
    console: { error() {} }, localStorage: { getItem: () => 'test-token' },
  };
  const reserve = vm.runInNewContext(`({${method}}).reserve_account`, sandbox);
  await reserve.call(context, 'palmpay');
  return { requests, messages, alerts, window, context };
}
(async () => {
  for (const user of [{ bvn: 'saved' }, { nin: 'saved' }, { bvn: 'saved', nin: 'saved' }]) {
    const result = await run(user, { success: true });
    assert.deepEqual(result.requests, ['/api/reserve/account/palmpay']);
    assert.equal(result.alerts.length, 1);
    assert.equal(result.window.location.href, '/dashboard');
    assert.equal(result.context.isCreatingReserve_palmpay, false);
    assert.equal(result.context.gra_errors_palmpay, false);
  }
  const missing = await run({}, { success: true });
  assert.equal(missing.requests.length, 0);
  assert.equal(missing.context.gra_errors_palmpay, true);
  assert.equal(missing.context.isCreatingReserve_palmpay, false);
  for (const response of [null, {}, { success: false, message: 'Unable to create account' }]) {
    const result = await run({ nin: 'saved' }, response);
    assert.equal(result.alerts.length, 0);
    assert.equal(result.window.location.href, '');
    assert.equal(result.context.isCreatingReserve_palmpay, false);
    assert.ok(result.messages.length > 1);
  }
  const failed = await run({ nin: 'saved' }, null, { response: { data: { message: 'PalmPay unavailable' } } });
  assert.equal(failed.messages.at(-1), 'PalmPay unavailable');
  assert.equal(failed.alerts.length, 0);
  assert.equal(failed.context.isCreatingReserve_palmpay, false);
  assert.match(source, /either BVN or NIN/);
  console.log('PalmPay dashboard regression checks passed.');
})().catch(error => { console.error(error); process.exitCode = 1; });
