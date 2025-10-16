<?php

namespace App\Http\Requests\TravelOrders;

use App\Dtos\TravelOrders\TravelOrderStatusDto;
use App\Enums\TravelOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TravelOrderStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    TravelOrderStatus::APROVADO->value,
                    TravelOrderStatus::CANCELADO->value,
                ]),
            ],
            'annotation' => ['nullable', 'string'],
        ];
    }

    public function toDto(string $travelOrderUuid): TravelOrderStatusDto
    {
        return new TravelOrderStatusDto(
            array_merge($this->validated(), ['uuid' => $travelOrderUuid])
        );
    }
}
