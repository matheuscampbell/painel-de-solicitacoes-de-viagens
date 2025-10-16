<?php

namespace App\Repositories;

use App\Dtos\TravelOrders\TravelOrderFilterDto;
use App\Enums\TravelOrderStatus;
use App\Models\TravelOrder;
use App\Services\Auth\AuthContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TravelOrderRepository
{
    public function __construct(
        private readonly AuthContext $authContext
    ) {
    }

    /**
     * @return Collection<int, TravelOrder>|LengthAwarePaginator
     */
    public function filter(TravelOrderFilterDto $dto, ?TravelOrderStatus $status = null): Collection|LengthAwarePaginator
    {
        $user = $this->authContext->user();
        $isAdmin = $user->isAdmin();

        $query = TravelOrder::query()
            ->with('requester:id,uuid,name');

        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        if ($status) {
            $query->where('status', $status->value);
        }

        if ($dto->origin) {
            $query->where('origin', 'like', '%' . $dto->origin . '%');
        }

        if ($dto->destination) {
            $query->where('destination', 'like', '%' . $dto->destination . '%');
        }

        if ($dto->created_from) {
            $query->whereDate('created_at', '>=', $dto->created_from);
        }

        if ($dto->created_to) {
            $query->whereDate('created_at', '<=', $dto->created_to);
        }

        if ($dto->travel_from && $dto->travel_to) {
            $query->whereDate('departure_date', '<=', $dto->travel_to)
                ->whereDate('return_date', '>=', $dto->travel_from);
        } elseif ($dto->travel_from) {
            $query->whereDate('return_date', '>=', $dto->travel_from);
        } elseif ($dto->travel_to) {
            $query->whereDate('departure_date', '<=', $dto->travel_to);
        }

        $query->orderByDesc('created_at');

        if ($dto->per_page) {
            return $query->paginate($dto->per_page)->withQueryString();
        }

        return $query->get();
    }

    public function create(array $data): TravelOrder
    {
        $travelOrder = new TravelOrder($data);
        $travelOrder->save();

        return $travelOrder->fresh(['requester:id,uuid,name']);
    }

    public function findOne(string $uuid): ?TravelOrder
    {
        return TravelOrder::with([
            'requester:id,uuid,name',
            'statusHistories' => fn ($query) => $query->with('changedBy:id,uuid,name')->orderByDesc('created_at'),
        ])->where('uuid', $uuid)->first();
    }

    public function save(TravelOrder $travelOrder): TravelOrder
    {
        $travelOrder->save();

        return $travelOrder->fresh(['requester:id,uuid,name']);
    }
}
