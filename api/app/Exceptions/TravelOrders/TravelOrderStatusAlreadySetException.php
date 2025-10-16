<?php

namespace App\Exceptions\TravelOrders;

use App\Exceptions\Base\BusinessException;

class TravelOrderStatusAlreadySetException extends BusinessException
{
    protected $message = 'O pedido já possui este status.';

    public function statusCode(): int
    {
        return 409;
    }
}
