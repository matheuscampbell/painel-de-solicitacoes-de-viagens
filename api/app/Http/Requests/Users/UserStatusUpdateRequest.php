<?php

namespace App\Http\Requests\Users;

use App\Dtos\Users\UserStatusDto;
use Illuminate\Foundation\Http\FormRequest;

class UserStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function toDto(string $userUuid): UserStatusDto
    {
        return new UserStatusDto(array_merge($this->validated(), ['uuid' => $userUuid]));
    }
}
