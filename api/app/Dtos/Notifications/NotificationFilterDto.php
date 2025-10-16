<?php

namespace App\Dtos\Notifications;

use App\Dtos\TinyDto;

class NotificationFilterDto extends TinyDto
{
    public ?int $per_page = null;
}

