<?php

namespace App\Services;

use App\Dtos\TravelOrders\TravelOrderFilterDto;
use App\Dtos\TravelOrders\TravelOrderIdentifierDto;
use App\Dtos\TravelOrders\TravelOrderStatusDto;
use App\Dtos\TravelOrders\TravelOrderStoreDto;
use App\Enums\TravelOrderStatus;
use App\Exceptions\TravelOrders\TravelOrderAccessDeniedException;
use App\Exceptions\TravelOrders\TravelOrderInvalidStatusException;
use App\Exceptions\TravelOrders\TravelOrderNotFoundException;
use App\Exceptions\TravelOrders\TravelOrderStatusAlreadySetException;
use App\Exceptions\TravelOrders\TravelOrderStatusTransitionException;
use App\Models\TravelOrder;
use App\Notifications\TravelOrderStatusNotification;
use App\Repositories\TravelOrderRepository;
use App\Repositories\TravelOrderStatusHistoryRepository;
use App\Services\Auth\AuthContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TravelOrderService
{
    public function __construct(
        private readonly TravelOrderRepository $travelOrderRepository,
        private readonly TravelOrderStatusHistoryRepository $statusHistoryRepository,
        private readonly AuthContext $authContext
    ) {
    }

    /**
     * @return Collection<int, TravelOrder>|LengthAwarePaginator
     *
     * @throws TravelOrderInvalidStatusException
     */
    public function list(TravelOrderFilterDto $dto): Collection|LengthAwarePaginator
    {
        $status = null;

        if ($dto->status !== null) {
            $status = TravelOrderStatus::tryFromName($dto->status);
            if ($status === null) {
                throw new TravelOrderInvalidStatusException();
            }
        }

        return $this->travelOrderRepository->filter($dto, $status);
    }

    public function create(TravelOrderStoreDto $dto): TravelOrder
    {
        return DB::transaction(function () use ($dto) {
            $data = $dto->toArray();
            $data['user_id'] = $this->authContext->id();
            $data['status'] = TravelOrderStatus::SOLICITADO;

            Log::info('Criando pedido de viagem.', [
                'user_id' => $data['user_id'],
                'destination' => $data['destination'],
                'origin' => $data['origin'] ?? null,
            ]);

            $travelOrder = $this->travelOrderRepository->create($data);

            Log::info('Pedido de viagem criado.', [
                'travel_order_id' => $travelOrder->id,
                'travel_order_uuid' => $travelOrder->uuid,
            ]);

            $this->statusHistoryRepository->create([
                'travel_order_id' => $travelOrder->id,
                'from_status' => null,
                'to_status' => TravelOrderStatus::SOLICITADO,
                'annotation' => 'Pedido criado.',
                'changed_by' => $data['user_id'],
            ]);

            return $this->travelOrderRepository->findOne($travelOrder->uuid);
        });
    }

    /**
     * @throws TravelOrderNotFoundException
     * @throws TravelOrderAccessDeniedException
     */
    public function show(TravelOrderIdentifierDto $dto): TravelOrder
    {
        $travelOrder = $this->travelOrderRepository->findOne($dto->uuid);

        if (!$travelOrder) {
            throw new TravelOrderNotFoundException();
        }

        $currentUser = $this->authContext->user();

        if (!$currentUser->isAdmin() && $travelOrder->user_id !== $currentUser->id) {
            throw new TravelOrderAccessDeniedException();
        }

        return $travelOrder;
    }

    /**
     * @throws TravelOrderAccessDeniedException
     * @throws TravelOrderNotFoundException
     * @throws TravelOrderInvalidStatusException
     * @throws TravelOrderStatusAlreadySetException
     * @throws TravelOrderStatusTransitionException
     */
    public function updateStatus(TravelOrderStatusDto $dto): TravelOrder
    {
        return DB::transaction(function () use ($dto) {
            if (!$this->authContext->isAdmin()) {
                throw new TravelOrderAccessDeniedException('Apenas administradores podem atualizar o status de pedidos de viagem.');
            }

            $travelOrder = $this->travelOrderRepository->findOne($dto->uuid);

            if (!$travelOrder) {
                throw new TravelOrderNotFoundException();
            }

            $newStatus = TravelOrderStatus::tryFromName($dto->status);

            if ($newStatus === null) {
                throw new TravelOrderInvalidStatusException();
            }

            $currentStatus = $travelOrder->status;

            if ($currentStatus === $newStatus) {
                throw new TravelOrderStatusAlreadySetException();
            }

            if (!$currentStatus->canTransitionTo($newStatus)) {
                throw new TravelOrderStatusTransitionException();
            }

            $travelOrder->status = $newStatus;
            $travelOrder = $this->travelOrderRepository->save($travelOrder);

            $this->statusHistoryRepository->create([
                'travel_order_id' => $travelOrder->id,
                'from_status' => $currentStatus,
                'to_status' => $newStatus,
                'annotation' => $dto->annotation,
                'changed_by' => $this->authContext->id(),
            ]);

            if (in_array($newStatus, [TravelOrderStatus::APROVADO, TravelOrderStatus::CANCELADO], true)) {
                $travelOrder->requester->notify(new TravelOrderStatusNotification($travelOrder, $currentStatus));
            }

            Log::info('Status do pedido de viagem atualizado.', [
                'travel_order_id' => $travelOrder->id,
                'travel_order_uuid' => $travelOrder->uuid,
                'new_status' => $newStatus->value,
            ]);

            return $this->travelOrderRepository->findOne($travelOrder->uuid);
        });
    }
}
