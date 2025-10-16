<?php

namespace App\Dtos\Users;

use App\Dtos\TinyDto;

class UserUpdateDto extends TinyDto
{
    public ?string $uuid = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $tipo_usuario = null;
    public ?bool $is_active = null;
}

