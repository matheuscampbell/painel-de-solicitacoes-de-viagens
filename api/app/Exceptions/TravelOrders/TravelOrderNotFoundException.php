<?php

namespace App\Exceptions\TravelOrders;

use App\Exceptions\Base\BusinessException;

class TravelOrderNotFoundException extends BusinessException
{
    protected $message = 'Pedido de viagem não encontrado.';

    public function statusCode(): int
    {
        return 404;
    }
}
