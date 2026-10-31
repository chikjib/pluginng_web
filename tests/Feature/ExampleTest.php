<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_login_shell_renders_with_existing_site_settings(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value');
        });
        DB::table('settings')->insert([
            ['key' => 'SOCIAL', 'value' => '||'],
            ['key' => 'LIVECHAT', 'value' => ''],
        ]);
        $this->get('/login')->assertOk()->assertSee('Pluginng');
    }
}
