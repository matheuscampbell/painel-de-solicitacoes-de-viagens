<?php

namespace App\Http\Requests\TravelOrders;

use App\Dtos\TravelOrders\TravelOrderFilterDto;
use App\Enums\TravelOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TravelOrderIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(TravelOrderStatus::values())],
            'destination' => ['nullable', 'string', 'max:255'],
            'origin' => ['nullable', 'string', 'max:255'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date', 'after_or_equal:created_from'],
            'travel_from' => ['nullable', 'date'],
            'travel_to' => ['nullable', 'date', 'after_or_equal:travel_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDto(): TravelOrderFilterDto
    {
        $data = $this->validated();

        if (isset($data['per_page'])) {
            $data['per_page'] = (int) $data['per_page'];
        }

        return new TravelOrderFilterDto($data);
    }
}
