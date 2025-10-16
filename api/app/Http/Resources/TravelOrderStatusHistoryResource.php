<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\TravelOrderStatusHistory
 */
class TravelOrderStatusHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'from_status' => $this->from_status?->value,
            'to_status' => $this->to_status->value,
            'annotation' => $this->annotation,
            'changed_by' => $this->whenLoaded('changedBy', fn () => [
                'uuid' => $this->changedBy?->uuid,
                'name' => $this->changedBy?->name,
            ]),
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
