<?php

namespace Tests\Feature;

use App\MonnifySignatureValidator;
use App\Traits\IntegrationsTrait;
use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookConfig;
use Tests\TestCase;

class WebhookUpgradeTest extends TestCase
{
    public function test_monnify_signatures_accept_valid_payloads_and_reject_tampering(): void
    {
        $settings = config('webhook-client.configs.0');
        $settings['signing_secret'] = 'test-only-secret';
        $config = new WebhookConfig($settings);
        $validator = new MonnifySignatureValidator;
        $payload = json_encode(['eventType' => 'SUCCESSFUL_TRANSACTION', 'amount' => 100]);
        $request = Request::create('/monify-webhook', 'POST', [], [], [], [], $payload);
        $request->headers->set('monnify-signature', hash_hmac('sha512', $payload, 'test-only-secret'));
        $this->assertTrue($validator->isValid($request, $config));
        $request->headers->set('monnify-signature', hash_hmac('sha512', $payload.'changed', 'test-only-secret'));
        $this->assertFalse($validator->isValid($request, $config));
        $request->headers->remove('monnify-signature');
        $this->assertFalse($validator->isValid($request, $config));
    }

    public function test_palmpay_callback_signature_verification_survives_the_upgrade(): void
    {
        $integration = new class { use IntegrationsTrait; };
        $key = openssl_pkey_get_private($_ENV['PASSPORT_PRIVATE_KEY']);
        $public = openssl_pkey_get_details($key)['key'];
        config(['app.palmPayPublicKey' => preg_replace('/-----[^-]+-----|\s+/', '', $public)]);
        $payload = ['orderNo' => 'TEST-ORDER', 'orderAmount' => 10000, 'orderStatus' => 1];
        $hash = strtoupper(md5($integration->params_sort($payload)));
        openssl_sign($hash, $signature, $key, OPENSSL_ALGO_SHA1);
        $payload['sign'] = base64_encode($signature);
        $this->assertSame(1, $integration->verifyPalmPayTrans($payload));
        $payload['sign'] = rawurlencode($payload['sign']);
        $this->assertSame(1, $integration->verifyPalmPayTrans($payload));
        $payload['orderAmount'] = 99999;
        $this->assertSame(0, $integration->verifyPalmPayTrans($payload));
    }
}
