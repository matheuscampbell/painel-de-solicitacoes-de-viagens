<?php

namespace Tests\Feature;

use App\Notifications\TravelOrderStatusNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create();

        $notification = DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => TravelOrderStatusNotification::class,
            'notifiable_id' => $user->id,
            'notifiable_type' => User::class,
            'data' => ['message' => 'Teste'],
            'read_at' => null,
        ]);

        $response = $this->actingAs($user, 'api')
            ->patchJson("/api/notifications/{$notification->id}/read");

        $response->assertOk()
            ->assertJsonPath('data.id', $notification->id)
            ->assertJsonPath('data.is_read', true);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_retrieve_unread_notification_count(): void
    {
        $user = User::factory()->create();

        DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => TravelOrderStatusNotification::class,
            'notifiable_id' => $user->id,
            'notifiable_type' => User::class,
            'data' => ['message' => 'Lida'],
            'read_at' => now(),
        ]);

        DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => TravelOrderStatusNotification::class,
            'notifiable_id' => $user->id,
            'notifiable_type' => User::class,
            'data' => ['message' => 'Não lida'],
            'read_at' => null,
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/notifications/unread-count');

        $response->assertOk()
            ->assertJsonPath('data.unread_count', 1)
            ->assertJsonPath('message', 'Contagem de notificações não lidas recuperada com sucesso.');
    }

    public function test_mark_notification_as_read_returns_not_found_for_unknown_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/notifications/' . Str::uuid() . '/read');

        $response->assertStatus(404)
            ->assertJsonPath('error', 'Notificação não encontrada.');
    }

    public function test_notifications_pagination_returns_meta(): void
    {
        $user = User::factory()->create();

        DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => TravelOrderStatusNotification::class,
            'notifiable_id' => $user->id,
            'notifiable_type' => User::class,
            'data' => ['message' => 'Teste 1'],
            'read_at' => null,
        ]);

        DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => TravelOrderStatusNotification::class,
            'notifiable_id' => $user->id,
            'notifiable_type' => User::class,
            'data' => ['message' => 'Teste 2'],
            'read_at' => null,
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/notifications?per_page=1');

        $response->assertOk()
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.last_page', 2);
    }
}
