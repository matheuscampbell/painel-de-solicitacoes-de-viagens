<?php

namespace App\Repositories;

use App\Models\TravelOrderStatusHistory;

class TravelOrderStatusHistoryRepository
{
    public function create(array $data): TravelOrderStatusHistory
    {
        return TravelOrderStatusHistory::create($data);
    }
}

