<?php

namespace App\Http\Requests\Users;

use App\Dtos\Users\UserStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'tipo_usuario' => ['required', 'in:admin,cliente'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function toDto(): UserStoreDto
    {
        return new UserStoreDto($this->validated());
    }
}

