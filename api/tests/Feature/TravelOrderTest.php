<?php

namespace Tests\Feature;

use App\Enums\TravelOrderStatus;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\TravelOrderStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TravelOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_travel_order(): void
    {
        $user = User::factory()->create();

        $payload = [
            'origin' => 'São Paulo',
            'destination' => 'Lisboa',
            'departure_date' => Carbon::now()->addDays(10)->toDateString(),
            'return_date' => Carbon::now()->addDays(15)->toDateString(),
            'notes' => 'Viagem a trabalho',
        ];

        $response = $this->actingAs($user, 'api')->postJson('/api/travel-orders', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.uuid', fn ($value) => !empty($value))
            ->assertJsonPath('data.destination', 'Lisboa')
            ->assertJsonPath('data.origin', 'São Paulo')
            ->assertJsonPath('data.status', TravelOrderStatus::SOLICITADO->value)
            ->assertJsonPath('data.requester.uuid', $user->uuid)
            ->assertJsonPath('data.status_history.0.to_status', TravelOrderStatus::SOLICITADO->value)
            ->assertJsonPath('data.status_history.0.annotation', 'Pedido criado.')
            ->assertJsonPath('message', 'Pedido de viagem criado com sucesso.')
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('travel_orders', [
            'destination' => 'Lisboa',
            'status' => TravelOrderStatus::SOLICITADO->value,
            'user_id' => $user->id,
            'origin' => 'São Paulo',
        ]);
    }

    public function test_cannot_create_travel_order_with_invalid_dates(): void
    {
        $user = User::factory()->create();

        $payload = [
            'origin' => 'São Paulo',
            'destination' => 'Lisboa',
            'departure_date' => Carbon::now()->addDays(10)->toDateString(),
            'return_date' => Carbon::now()->addDays(5)->toDateString(),
        ];

        $response = $this->actingAs($user, 'api')->postJson('/api/travel-orders', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['return_date']);
    }

    public function test_non_admin_cannot_update_travel_order_status(): void
    {
        $requester = User::factory()->create();
        $nonAdmin = User::factory()->create();

        $travelOrder = TravelOrder::factory()->for($requester, 'requester')->create([
            'status' => TravelOrderStatus::SOLICITADO,
        ]);

        $response = $this->actingAs($nonAdmin, 'api')
            ->patchJson("/api/travel-orders/{$travelOrder->uuid}/status", [
                'status' => TravelOrderStatus::APROVADO->value,
            ]);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'This action is unauthorized.');
    }

    public function test_user_cannot_view_travel_order_from_another_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $travelOrder = TravelOrder::factory()->for($owner, 'requester')->create();

        $response = $this->actingAs($otherUser, 'api')
            ->getJson("/api/travel-orders/{$travelOrder->uuid}");

        $response->assertStatus(403)
            ->assertJsonPath('error', 'Você não tem permissão para acessar este pedido de viagem.');
    }

    public function test_admin_can_approve_travel_order_and_notify_requester(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['tipo_usuario' => 'admin']);
        $requester = User::factory()->create();

        $travelOrder = TravelOrder::factory()
            ->for($requester, 'requester')
            ->create([
                'status' => TravelOrderStatus::SOLICITADO,
            ]);

        $response = $this->actingAs($admin, 'api')
            ->patchJson("/api/travel-orders/{$travelOrder->uuid}/status", [
                'status' => TravelOrderStatus::APROVADO->value,
                'annotation' => 'Aprovado após análise.',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.uuid', $travelOrder->uuid)
            ->assertJsonPath('data.status', TravelOrderStatus::APROVADO->value)
            ->assertJsonPath('data.status_history.0.to_status', TravelOrderStatus::APROVADO->value)
            ->assertJsonPath('data.status_history.0.annotation', 'Aprovado após análise.')
            ->assertJsonPath('message', 'Status do pedido de viagem atualizado com sucesso.')
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('travel_orders', [
            'id' => $travelOrder->id,
            'status' => TravelOrderStatus::APROVADO->value,
        ]);

        Notification::assertSentTo(
            $requester,
            TravelOrderStatusNotification::class,
            function (TravelOrderStatusNotification $notification) use ($requester, $travelOrder) {
                $data = $notification->toArray($requester);

                return $data['status'] === TravelOrderStatus::APROVADO->value
                    && $data['travel_order_uuid'] === $travelOrder->uuid;
            }
        );
    }

    public function test_admin_cannot_cancel_order_after_approval(): void
    {
        $admin = User::factory()->create(['tipo_usuario' => 'admin']);

        $travelOrder = TravelOrder::factory()->approved()->create();

        $response = $this->actingAs($admin, 'api')
            ->patchJson("/api/travel-orders/{$travelOrder->uuid}/status", [
                'status' => TravelOrderStatus::CANCELADO->value,
                'annotation' => 'Cancelamento solicitado.',
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'error' => 'Não é possível atualizar o pedido para o status solicitado.',
                'status' => 'error',
                'success' => false,
            ]);

        $this->assertDatabaseHas('travel_orders', [
            'id' => $travelOrder->id,
            'status' => TravelOrderStatus::APROVADO->value,
        ]);
    }

    public function test_user_can_list_notifications(): void
    {
        $admin = User::factory()->create(['tipo_usuario' => 'admin']);
        $requester = User::factory()->create();

        $travelOrder = TravelOrder::factory()
            ->for($requester, 'requester')
            ->create([
                'status' => TravelOrderStatus::SOLICITADO,
            ]);

        $this->actingAs($admin, 'api')
            ->patchJson("/api/travel-orders/{$travelOrder->uuid}/status", [
                'status' => TravelOrderStatus::APROVADO->value,
            ])
            ->assertOk();

        $response = $this->actingAs($requester, 'api')
            ->getJson('/api/notifications');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Notificações listadas com sucesso.')
            ->assertJsonPath('data.0.data.travel_order_uuid', $travelOrder->uuid);
    }

    public function test_travel_orders_pagination_returns_meta(): void
    {
        $user = User::factory()->create();

        TravelOrder::factory()->count(3)->for($user, 'requester')->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/travel-orders?per_page=2');

        $response->assertOk()
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('data.0.uuid', fn ($value) => !empty($value));
    }
}
