<?php

namespace App\Dtos\Locations;

use App\Dtos\TinyDto;

class CityLookupDto extends TinyDto
{
    public ?string $query = null;
    public ?string $state = null;
    public ?int $limit = null;
}

