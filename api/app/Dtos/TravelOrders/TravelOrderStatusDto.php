<?php

namespace App\Dtos\TravelOrders;

use App\Dtos\TinyDto;

class TravelOrderStatusDto extends TinyDto
{
    public ?string $uuid = null;
    public ?string $status = null;
    public ?string $annotation = null;
}
