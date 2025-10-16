<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['tipo_usuario' => 'admin']);

        $payload = [
            'name' => 'João Gestor',
            'email' => 'gestor@example.com',
            'password' => 'secret123',
            'tipo_usuario' => 'cliente',
        ];

        $response = $this->actingAs($admin, 'api')
            ->postJson('/api/admin/users', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'gestor@example.com')
            ->assertJsonPath('data.uuid', fn ($value) => !empty($value))
            ->assertJsonPath('message', 'Usuário criado com sucesso.')
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('users', [
            'email' => 'gestor@example.com',
            'tipo_usuario' => 'cliente',
            'is_active' => true,
        ]);
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/admin/users');

        $response->assertStatus(403)
            ->assertJsonPath('error', 'Você não tem permissão para executar esta ação.');
    }

    public function test_admin_can_deactivate_user(): void
    {
        $admin = User::factory()->create(['tipo_usuario' => 'admin']);
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/users/{$user->uuid}/status", [
                'is_active' => false,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.is_active', false)
            ->assertJsonPath('message', 'Usuário desativado com sucesso.')
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_update_user_and_change_password(): void
    {
        $admin = User::factory()->create(['tipo_usuario' => 'admin']);
        $user = User::factory()->create([
            'email' => 'alvo@example.com',
            'password' => 'oldpassword',
        ]);

        $payload = [
            'name' => 'Usuário Atualizado',
            'email' => 'alvo-novo@example.com',
            'password' => 'novasenha',
            'tipo_usuario' => 'cliente',
        ];

        $response = $this->actingAs($admin, 'api')
            ->patchJson("/api/admin/users/{$user->uuid}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Usuário Atualizado')
            ->assertJsonPath('data.email', 'alvo-novo@example.com')
            ->assertJsonPath('message', 'Usuário atualizado com sucesso.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'alvo-novo@example.com',
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'alvo-novo@example.com',
            'password' => 'novasenha',
        ]);

        $loginResponse->assertOk()
            ->assertJsonPath('token_type', 'bearer');
    }

    public function test_admin_user_list_returns_pagination_meta(): void
    {
        $admin = User::factory()->create(['tipo_usuario' => 'admin']);
        User::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'api')
            ->getJson('/api/admin/users?per_page=2');

        $response->assertOk()
            ->assertJsonPath('meta.total', 4)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.last_page', 2);
    }
}
