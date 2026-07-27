<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExternalLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('liof_user_id')->nullable()->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        config()->set('services.liof_login.url', 'https://login.example.test/api_login.php');
        config()->set('services.liof_login.token', 'test-token');
    }

    public function test_an_external_admin_is_logged_in_with_admin_role(): void
    {
        Http::fake([
            'https://login.example.test/*' => Http::response([
                'success' => true,
                'id' => 1001,
                'username' => 'admin.liof',
                'operatore' => 'Admin Liof',
                'user_camp' => 1,
            ]),
        ]);

        $response = $this->post(route('login.attempt'), [
            'username' => 'admin.liof',
            'password' => 'password-api',
        ]);

        $response->assertRedirect(route('monitoraggi.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'liof_user_id' => 1001,
            'name' => 'Admin Liof',
            'role' => 'admin',
        ]);
        Http::assertSent(fn (Request $request): bool => $request->data() === [
            'action' => 'login',
            'app_name' => 'campionamenti',
            'api_token' => 'test-token',
            'username' => 'admin.liof',
            'password' => 'password-api',
        ]);
    }

    public function test_an_external_user_is_logged_in_with_operatore_role(): void
    {
        Http::fake([
            'https://login.example.test/*' => Http::response([
                'success' => true,
                'id' => 1002,
                'username' => 'operator.liof',
                'operatore' => 'Operatore Liof',
                'user_camp' => 2,
            ]),
        ]);

        $response = $this->post(route('login.attempt'), [
            'username' => 'operator.liof',
            'password' => 'password-api',
        ]);

        $response->assertRedirect(route('monitoraggi.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'liof_user_id' => 1002,
            'role' => 'operatore',
        ]);
    }

    public function test_an_external_user_without_an_enabled_role_is_rejected(): void
    {
        Http::fake([
            'https://login.example.test/*' => Http::response([
                'success' => true,
                'id' => 1003,
                'username' => 'blocked.liof',
                'user_camp' => 3,
            ]),
        ]);

        $response = $this->from(route('login'))
            ->post(route('login.attempt'), [
                'username' => 'blocked.liof',
                'password' => 'password-api',
            ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['liof_user_id' => 1003]);
    }
}