<?php

namespace App\Exceptions\TravelOrders;

use App\Exceptions\Base\BusinessException;

class TravelOrderInvalidStatusException extends BusinessException
{
    protected $message = 'Status informado é inválido.';

    public function statusCode(): int
    {
        return 422;
    }
}
