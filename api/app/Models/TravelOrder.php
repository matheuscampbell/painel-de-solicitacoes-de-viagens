<?php

namespace App\Models;

use App\Enums\TravelOrderStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelOrder extends Model
{
    use HasFactory, UsesUuid;

    protected $fillable = [
        'user_id',
        'origin',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'notes',
        'uuid',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'status' => TravelOrderStatus::class,
        'uuid' => 'string',
    ];

    protected $appends = [
        'requester_name',
    ];

    protected $hidden = [
        'id',
        'user_id',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TravelOrderStatusHistory::class)->orderByDesc('created_at');
    }

    public function getRequesterNameAttribute(): ?string
    {
        return $this->requester?->name;
    }

    public function scopeWithStatus($query, ?TravelOrderStatus $status)
    {
        if ($status === null) {
            return $query;
        }

        return $query->where('status', $status->value);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
