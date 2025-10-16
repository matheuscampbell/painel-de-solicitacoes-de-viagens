<?php

namespace Tests\Unit;

use App\Dtos\TravelOrders\TravelOrderFilterDto;
use App\Dtos\TravelOrders\TravelOrderIdentifierDto;
use App\Dtos\TravelOrders\TravelOrderStatusDto;
use App\Dtos\TravelOrders\TravelOrderStoreDto;
use App\Enums\TravelOrderStatus;
use App\Exceptions\TravelOrders\TravelOrderAccessDeniedException;
use App\Exceptions\TravelOrders\TravelOrderInvalidStatusException;
use App\Exceptions\TravelOrders\TravelOrderNotFoundException;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\TravelOrderStatusNotification;
use App\Repositories\TravelOrderRepository;
use App\Repositories\TravelOrderStatusHistoryRepository;
use App\Services\Auth\AuthContext;
use App\Services\TravelOrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class TravelOrderServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        Log::shouldReceive('info')->byDefault();
    }

    public function test_list_throws_exception_for_invalid_status(): void
    {
        $service = new TravelOrderService(
            Mockery::mock(TravelOrderRepository::class),
            Mockery::mock(TravelOrderStatusHistoryRepository::class),
            Mockery::mock(AuthContext::class)
        );

        $dto = new TravelOrderFilterDto(['status' => 'invalido']);

        $this->expectException(TravelOrderInvalidStatusException::class);

        $service->list($dto);
    }

    public function test_create_travel_order_persists_and_returns_with_history(): void
    {
        $travelOrderRepository = Mockery::mock(TravelOrderRepository::class);
        $historyRepository = Mockery::mock(TravelOrderStatusHistoryRepository::class);
        $authContext = Mockery::mock(AuthContext::class);

        $service = new TravelOrderService($travelOrderRepository, $historyRepository, $authContext);

        $dto = new TravelOrderStoreDto([
            'origin' => 'São Paulo',
            'destination' => 'Lisboa',
            'departure_date' => '2025-10-10',
            'return_date' => '2025-10-15',
        ]);

        $authContext->shouldReceive('id')->once()->andReturn(10);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $travelOrder = new TravelOrder();
        $travelOrder->forceFill([
            'id' => 123,
            'uuid' => (string) Str::uuid(),
            'user_id' => 10,
            'origin' => 'São Paulo',
            'destination' => 'Lisboa',
            'departure_date' => '2025-10-10',
            'return_date' => '2025-10-15',
            'notes' => null,
        ]);
        $travelOrder->status = TravelOrderStatus::SOLICITADO;

        $travelOrderRepository->shouldReceive('create')
            ->atLeast()->once()
            ->andReturn($travelOrder);

        $historyRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data) use ($travelOrder) {
                return $data['travel_order_id'] === $travelOrder->id
                    && $data['to_status']->value === TravelOrderStatus::SOLICITADO->value
                    && $data['changed_by'] === 10;
            }));

        $travelOrderRepository->shouldReceive('findOne')
            ->once()
            ->with($travelOrder->uuid)
            ->andReturn($travelOrder);

        $result = $service->create($dto);

        $this->assertSame($travelOrder, $result);
    }

    public function test_update_status_requires_admin(): void
    {
        $travelOrderRepository = Mockery::mock(TravelOrderRepository::class);
        $historyRepository = Mockery::mock(TravelOrderStatusHistoryRepository::class);
        $authContext = Mockery::mock(AuthContext::class);

        $service = new TravelOrderService($travelOrderRepository, $historyRepository, $authContext);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $authContext->shouldReceive('isAdmin')->once()->andReturn(false);

        $dto = new TravelOrderStatusDto([
            'uuid' => (string) Str::uuid(),
            'status' => TravelOrderStatus::APROVADO->value,
        ]);

        $this->expectException(TravelOrderAccessDeniedException::class);

        $service->updateStatus($dto);
    }

    public function test_update_status_transitions_and_notifies_requester(): void
    {
        $travelOrderRepository = Mockery::mock(TravelOrderRepository::class);
        $historyRepository = Mockery::mock(TravelOrderStatusHistoryRepository::class);
        $authContext = Mockery::mock(AuthContext::class);

        $service = new TravelOrderService($travelOrderRepository, $historyRepository, $authContext);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $uuid = (string) Str::uuid();

        $requester = Mockery::mock(User::class)->makePartial();
        $requester->shouldReceive('notify')->once()->with(Mockery::type(TravelOrderStatusNotification::class));

        $travelOrder = new TravelOrder();
        $travelOrder->forceFill([
            'id' => 55,
            'uuid' => $uuid,
            'user_id' => 99,
            'origin' => 'Curitiba',
            'destination' => 'Paris',
            'departure_date' => '2025-12-01',
            'return_date' => '2025-12-10',
        ]);
        $travelOrder->status = TravelOrderStatus::SOLICITADO;
        $travelOrder->setRelation('requester', $requester);

        $updatedOrder = clone $travelOrder;
        $updatedOrder->status = TravelOrderStatus::APROVADO;
        $updatedOrder->setRelation('requester', $requester);

        $authContext->shouldReceive('isAdmin')->once()->andReturn(true);
        $authContext->shouldReceive('id')->once()->andReturn(1);

        $travelOrderRepository->shouldReceive('findOne')
            ->once()
            ->with($uuid)
            ->andReturn($travelOrder);

        $travelOrderRepository->shouldReceive('save')
            ->once()
            ->with($travelOrder)
            ->andReturn($travelOrder);

        $historyRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data) use ($travelOrder) {
                return $data['travel_order_id'] === $travelOrder->id
                    && $data['from_status']->value === TravelOrderStatus::SOLICITADO->value
                    && $data['to_status']->value === TravelOrderStatus::APROVADO->value
                    && $data['changed_by'] === 1;
            }));

        $travelOrderRepository->shouldReceive('findOne')
            ->once()
            ->with($uuid)
            ->andReturn($updatedOrder);

        $dto = new TravelOrderStatusDto([
            'uuid' => $uuid,
            'status' => TravelOrderStatus::APROVADO->value,
            'annotation' => 'Aprovado em teste',
        ]);

        $result = $service->updateStatus($dto);

        $this->assertSame($updatedOrder, $result);
        $this->assertEquals(TravelOrderStatus::APROVADO, $travelOrder->status);
    }

    public function test_show_throws_when_order_not_found(): void
    {
        $travelOrderRepository = Mockery::mock(TravelOrderRepository::class);
        $historyRepository = Mockery::mock(TravelOrderStatusHistoryRepository::class);
        $authContext = Mockery::mock(AuthContext::class);

        $service = new TravelOrderService($travelOrderRepository, $historyRepository, $authContext);

        $uuid = (string) Str::uuid();

        $travelOrderRepository->shouldReceive('findOne')
            ->once()
            ->with($uuid)
            ->andReturn(null);

        $this->expectException(TravelOrderNotFoundException::class);

        $service->show(new TravelOrderIdentifierDto(['uuid' => $uuid]));
    }
}
