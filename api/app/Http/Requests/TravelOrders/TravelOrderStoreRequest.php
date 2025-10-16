<?php

namespace App\Http\Requests\TravelOrders;

use App\Dtos\TravelOrders\TravelOrderStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class TravelOrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function toDto(): TravelOrderStoreDto
    {
        return new TravelOrderStoreDto($this->validated());
    }
}
