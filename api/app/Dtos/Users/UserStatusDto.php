<?php

namespace App\Dtos\Users;

use App\Dtos\TinyDto;

class UserStatusDto extends TinyDto
{
    public ?string $uuid = null;
    public ?bool $is_active = null;
}
