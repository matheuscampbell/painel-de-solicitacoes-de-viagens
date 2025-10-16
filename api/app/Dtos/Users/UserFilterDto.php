<?php

namespace App\Dtos\Users;

use App\Dtos\TinyDto;

class UserFilterDto extends TinyDto
{
    public ?string $search = null;
    public ?string $tipo_usuario = null;
    public ?string $status = null;
    public ?int $per_page = null;
}

