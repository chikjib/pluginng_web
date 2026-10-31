<?php

namespace Tests\Feature;

use App\AppConstants;
use App\Traits\IntegrationsTrait;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpgradeConfigurationTest extends TestCase
{
    public function test_legacy_sim_server_requery_reads_runtime_configuration(): void
    {
        config(['app.simServerBaseUrl' => 'https://sim.example.test/api', 'app.simServerApiKey' => 'test-key']);
        Http::preventStrayRequests();
        Http::fake(['https://sim.example.test/api' => Http::response(['status' => 'delivered'])]);
        $integration = new class { use IntegrationsTrait; };
        $this->assertSame('test-key', AppConstants::get('simServerApiKey'));
        $this->assertSame(['status' => 'delivered'], $integration->SIMServerRequery('order-123'));
        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request['api_key'] === 'test-key' && $request['order_id'] === 'order-123');
    }
    public function test_existing_session_and_cache_namespaces_are_explicit(): void
    {
        $this->assertSame('php', config('session.serialization'));
        $this->assertSame('pluginng_session', config('session.cookie'));
        $this->assertSame('pluginng_cache_:', config('cache.prefix'));
        $this->assertSame('pluginng_database_', config('database.redis.options.prefix'));
        $this->assertTrue(config('cache.serializable_classes'));
    }

    public function test_public_payment_metadata_reads_cached_configuration(): void
    {
        config(['app.ravePublicKey' => 'public-test-key']);
        $resource = new \App\Http\Resources\SettingResource((object) [
            'bonus' => 0, 'fundmin' => 100, 'fundmax' => 100000,
        ]);
        $this->assertSame('public-test-key', $resource->with(request())['meta']['rave_publickey']);
    }

}
