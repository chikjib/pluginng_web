<?php

namespace Tests\Feature;

use App\Exports\ExportOrder;
use App\Jobs\PalmpayProcessWebhookJob;
use App\Models\User;
use App\Traits\WalletTrait;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Auth, DB, Http, Schema};
use Maatwebsite\Excel\Facades\Excel;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\WebhookClient\Models\WebhookCall;
use Tests\TestCase;

class FinancialUpgradeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('palmpay_account')->nullable();
            $table->decimal('wallet', 16, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('category', function (Blueprint $table) {
            $table->id(); $table->string('title');
        });
        Schema::create('subcategory', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('category_id'); $table->string('title');
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('ref');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->string('plan');
            foreach (['amount', 'bal', 'prev_bal', 'subtotal', 'total'] as $column) $table->decimal($column, 16, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->text('description')->nullable();
            foreach (['phone', 'iuc', 'meter', 'response'] as $column) $table->string($column)->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
        DB::table('users')->insert(['id' => 1, 'email' => 'test@example.test', 'wallet' => 1000, 'palmpay_account' => '0123456789',
            'created_at' => now(), 'updated_at' => now()]);
        DB::table('category')->insert(['id' => 1, 'title' => 'Wallet Funding']);
        DB::table('subcategory')->insert(['id' => 1, 'category_id' => 1, 'title' => 'Palmpay']);
    }

    public function test_wallet_debit_and_credit_keep_decimal_balances(): void
    {
        $wallet = new class { use WalletTrait; };
        $this->assertTrue($wallet->isDebited(10.25, 1));
        $this->assertEquals(989.75, User::find(1)->wallet);
        $this->assertTrue($wallet->isCredited(5.50, 1));
        $this->assertEquals(995.25, User::find(1)->wallet);
    }

    public static function deposits(): array
    {
        return ['percentage fee' => [100000, 1996], 'capped fee' => [10000000, 100700]];
    }

    #[DataProvider('deposits')]
    public function test_signed_palmpay_deposit_is_credited_once(int $minorAmount, int $balance): void
    {
        $key = openssl_pkey_get_private($_ENV['PASSPORT_PRIVATE_KEY']);
        config(['app.palmPayPublicKey' => preg_replace('/-----[^-]+-----|\s+/', '', openssl_pkey_get_details($key)['key'])]);
        $payload = ['orderNo' => 'TEST-FUNDING', 'orderAmount' => $minorAmount, 'orderStatus' => 1, 'virtualAccountNo' => '0123456789'];
        $integration = new class { use \App\Traits\IntegrationsTrait; };
        openssl_sign(strtoupper(md5($integration->params_sort($payload))), $signature, $key, OPENSSL_ALGO_SHA1);
        $payload['sign'] = base64_encode($signature);
        $job = new PalmpayProcessWebhookJob(new WebhookCall(['payload' => $payload]));
        $job->handle();
        $this->assertEquals($balance, User::find(1)->wallet);
        $this->assertSame(1, DB::table('orders')->where('ref', 'TEST-FUNDING')->count());
        $job->handle();
        $this->assertEquals($balance, User::find(1)->wallet);
        $this->assertSame(1, DB::table('orders')->count());
        Http::assertNothingSent();
    }

    public function test_tampered_palmpay_deposit_does_not_credit_wallet(): void
    {
        $key = openssl_pkey_get_private($_ENV['PASSPORT_PRIVATE_KEY']);
        config(['app.palmPayPublicKey' => preg_replace('/-----[^-]+-----|\s+/', '', openssl_pkey_get_details($key)['key'])]);
        $payload = ['orderNo' => 'INVALID', 'orderAmount' => 100000, 'orderStatus' => 1, 'virtualAccountNo' => '0123456789'];
        openssl_sign('incorrect-payload', $signature, $key, OPENSSL_ALGO_SHA1);
        $payload['sign'] = base64_encode($signature);
        (new PalmpayProcessWebhookJob(new WebhookCall(['payload' => $payload])))->handle();
        $this->assertEquals(1000, User::find(1)->wallet);
        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_transaction_export_keeps_date_filter_and_status_mapping(): void
    {
        Auth::login(User::findOrFail(1));
        foreach (['2026-10-22' => 'IN-RANGE', '2026-10-20' => 'OUTSIDE-RANGE'] as $day => $ref) {
            DB::table('orders')->insert(['ref' => $ref, 'user_id' => 1, 'subcategory_id' => 1, 'plan' => 'Palmpay',
                'amount' => 99.50, 'status' => 1, 'created_at' => "$day 12:00:00", 'updated_at' => "$day 12:00:00"]);
        }
        $csv = Excel::raw(new ExportOrder('2026-10-22', '2026-10-22'), \Maatwebsite\Excel\Excel::CSV);
        $this->assertStringContainsString('IN-RANGE', $csv);
        $this->assertStringNotContainsString('OUTSIDE-RANGE', $csv);
        $this->assertStringContainsString('Confirmed', $csv);
        $this->assertStringContainsString('Wallet Funding', $csv);
    }
}
