<?php

namespace App\Dtos\TravelOrders;

use App\Dtos\TinyDto;

class TravelOrderStoreDto extends TinyDto
{
    public ?string $origin = null;
    public ?string $destination = null;
    public ?string $departure_date = null;
    public ?string $return_date = null;
    public ?string $notes = null;
}
