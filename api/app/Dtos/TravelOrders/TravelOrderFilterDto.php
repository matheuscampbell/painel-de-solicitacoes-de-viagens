<?php

namespace App\Dtos\TravelOrders;

use App\Dtos\TinyDto;

class TravelOrderFilterDto extends TinyDto
{
    public ?string $status = null;
    public ?string $destination = null;
    public ?string $origin = null;
    public ?string $created_from = null;
    public ?string $created_to = null;
    public ?string $travel_from = null;
    public ?string $travel_to = null;
    public ?int $per_page = null;
}
