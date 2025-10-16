<?php

namespace App\Http\Requests\Notifications;

use App\Dtos\Notifications\NotificationFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class NotificationIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDto(): NotificationFilterDto
    {
        $data = $this->validated();

        if (isset($data['per_page'])) {
            $data['per_page'] = (int) $data['per_page'];
        }

        return new NotificationFilterDto($data);
    }
}

