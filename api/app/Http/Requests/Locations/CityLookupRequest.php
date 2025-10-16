<?php

namespace App\Http\Requests\Locations;

use App\Dtos\Locations\CityLookupDto;
use Illuminate\Foundation\Http\FormRequest;

class CityLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2'],
            'state' => ['nullable', 'string', 'size:2'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function toDto(): CityLookupDto
    {
        $data = $this->validated();

        if (isset($data['limit'])) {
            $data['limit'] = (int) $data['limit'];
        }

        return new CityLookupDto($data);
    }
}

