<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Http, Schema};
use Laravel\Passport\Passport;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PalmpayUpgradeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table->text('bvn')->nullable();
            $table->text('nin')->nullable();
            $table->text('palmpay_reserved_acct')->nullable();
            $table->string('palmpay_account')->nullable();
            $table->timestamps();
        });
        Schema::create('withdrawal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
        });
        DB::table('users')->insert([
            'id' => 1, 'firstname' => 'Test', 'lastname' => 'Customer',
            'email' => 'test@example.test', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $privateKey = preg_replace('/-----[^-]+-----|\s+/', '', $_ENV['PASSPORT_PRIVATE_KEY']);
        config(['app.palmPayUrl' => 'https://palmpay.example.test/', 'app.palmPayAppID' => 'test-app',
            'app.palmPayPrivateKey' => $privateKey]);
        Http::preventStrayRequests();
    }

    private function providerResponse(): array
    {
        return ['respCode' => '00000000', 'status' => true, 'respMsg' => 'success',
            'data' => ['virtualAccountNo' => '0123456789', 'virtualAccountName' => 'Test Customer']];
    }

    private function authenticate(?string $bvn, ?string $nin): User
    {
        $user = User::findOrFail(1);
        $encrypt = fn ($id) => $id === null ? null : openssl_encrypt($id, 'AES-128-CTR', 'SWIFtlInk1nG', 0, 'SWiftLiNknG89101');
        $user->bvn = $encrypt($bvn);
        $user->nin = $encrypt($nin);
        $user->save();
        Passport::actingAs($user);
        return $user;
    }

    public static function identities(): array
    {
        return [
            'BVN' => ['01234567890', null, 'personal', '01234567890'],
            'NIN' => [null, '10987654321', 'personal_nin', '10987654321'],
            'both prefer BVN' => ['01234567890', '10987654321', 'personal', '01234567890'],
        ];
    }

    #[DataProvider('identities')]
    public function test_account_creation_sends_the_correct_identifier(?string $bvn, ?string $nin, string $type, string $id): void
    {
        Http::fake(['https://palmpay.example.test/*' => Http::response($this->providerResponse())]);
        $user = $this->authenticate($bvn, $nin);
        $this->getJson('/api/reserve/account/palmpay')->assertOk()->assertJsonPath('success', true);
        Http::assertSent(fn ($request) => $request['identityType'] === $type
            && $request['licenseNumber'] === $id && $request->hasHeader('Signature'));
        $this->assertSame('0123456789', $user->fresh()->palmpay_account);
        $this->assertSame('0123456789', json_decode($user->fresh()->palmpay_reserved_acct, true)['accountNumber']);
        $this->getJson('/api/reserve/account/palmpay')->assertOk();
        Http::assertSentCount(1);
    }

    public function test_no_identifier_returns_a_useful_error(): void
    {
        $this->authenticate(null, null);
        $this->getJson('/api/reserve/account/palmpay')->assertUnprocessable()->assertJsonPath('success', false);
        Http::assertNothingSent();
    }

    public function test_invalid_saved_identifier_is_rejected_without_calling_provider(): void
    {
        $this->authenticate(null, 'invalid');
        $this->getJson('/api/reserve/account/palmpay')->assertUnprocessable();
        Http::assertNothingSent();
    }

    public static function failures(): array
    {
        return ['rejected' => [['status' => false, 'respCode' => 'REJECTED']],
            'missing fields' => [['status' => true, 'respCode' => '00000000', 'data' => []]],
            'not JSON' => ['unavailable']];
    }

    #[DataProvider('failures')]
    public function test_provider_failure_does_not_save_a_broken_account($response): void
    {
        $user = $this->authenticate(null, '10987654321');
        Http::fake(['*' => Http::response($response, 502)]);
        $this->getJson('/api/reserve/account/palmpay')->assertStatus(502)->assertJsonPath('success', false);
        $this->assertNull($user->fresh()->palmpay_account);
        $this->assertNull($user->fresh()->palmpay_reserved_acct);
    }
}
