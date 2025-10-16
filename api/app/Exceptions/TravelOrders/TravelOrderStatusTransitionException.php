<?php

namespace App\Exceptions\TravelOrders;

use App\Exceptions\Base\BusinessException;

class TravelOrderStatusTransitionException extends BusinessException
{
    protected $message = 'Não é possível atualizar o pedido para o status solicitado.';

    public function statusCode(): int
    {
        return 409;
    }
}
