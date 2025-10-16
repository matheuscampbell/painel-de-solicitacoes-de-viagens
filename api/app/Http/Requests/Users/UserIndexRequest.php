<?php

namespace App\Http\Requests\Users;

use App\Dtos\Users\UserFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'tipo_usuario' => ['nullable', 'in:admin,cliente'],
            'status' => ['nullable', 'in:active,inactive'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDto(): UserFilterDto
    {
        $data = $this->validated();

        if (isset($data['per_page'])) {
            $data['per_page'] = (int) $data['per_page'];
        }

        return new UserFilterDto($data);
    }
}

