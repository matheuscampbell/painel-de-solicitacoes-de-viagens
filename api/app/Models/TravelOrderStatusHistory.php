<?php

namespace App\Models;

use App\Enums\TravelOrderStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelOrderStatusHistory extends Model
{
    use HasFactory, UsesUuid;

    protected $fillable = [
        'travel_order_id',
        'from_status',
        'to_status',
        'annotation',
        'changed_by',
        'uuid',
    ];

    protected $casts = [
        'from_status' => TravelOrderStatus::class,
        'to_status' => TravelOrderStatus::class,
        'uuid' => 'string',
    ];

    public function travelOrder(): BelongsTo
    {
        return $this->belongsTo(TravelOrder::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    protected $hidden = [
        'id',
        'travel_order_id',
        'changed_by',
    ];
}
