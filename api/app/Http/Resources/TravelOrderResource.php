<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'destination' => $this->destination,
            'origin' => $this->origin,
            'departure_date' => optional($this->departure_date)->toDateString(),
            'return_date' => optional($this->return_date)->toDateString(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'notes' => $this->notes,
            'requester' => $this->whenLoaded('requester', function () {
                return [
                    'uuid' => $this->requester?->uuid,
                    'name' => $this->requester?->name,
                ];
            }),
            'status_history' => $this->whenLoaded(
                'statusHistories',
                fn () => TravelOrderStatusHistoryResource::collection($this->statusHistories)
            ),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
