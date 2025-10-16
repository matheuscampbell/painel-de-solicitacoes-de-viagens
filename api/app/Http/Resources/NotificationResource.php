<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Illuminate\Notifications\DatabaseNotification
 */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payload = $this->data;

        if (isset($payload['travel_order_id']) && !isset($payload['travel_order_uuid'])) {
            $payload['travel_order_uuid'] = $payload['travel_order_id'];
            unset($payload['travel_order_id']);
        }

        if (isset($payload['travel_order_uuid']) && is_numeric($payload['travel_order_uuid'])) {
            $travelOrder = \App\Models\TravelOrder::find($payload['travel_order_uuid']);
            $payload['travel_order_uuid'] = $travelOrder?->uuid;
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'data' => $payload,
            'message' => $payload['message'] ?? null,
            'is_read' => $this->read_at !== null,
            'read_at' => optional($this->read_at)?->toISOString(),
            'created_at' => optional($this->created_at)?->toISOString(),
        ];
    }
}
