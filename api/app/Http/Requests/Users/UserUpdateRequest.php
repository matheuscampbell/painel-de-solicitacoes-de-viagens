<?php

namespace App\Http\Requests\Users;

use App\Dtos\Users\UserUpdateDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $uuid = $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($uuid, 'uuid'),
            ],
            'password' => ['sometimes', 'required', 'string', 'min:6'],
            'tipo_usuario' => ['sometimes', 'required', 'in:admin,cliente'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function toDto(string $userUuid): UserUpdateDto
    {
        return new UserUpdateDto(array_merge($this->validated(), ['uuid' => $userUuid]));
    }
}

