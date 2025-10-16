<?php

namespace App\Exceptions\TravelOrders;

use App\Exceptions\Base\BusinessException;

class TravelOrderAccessDeniedException extends BusinessException
{
    protected $message = 'Você não tem permissão para acessar este pedido de viagem.';

    public function statusCode(): int
    {
        return 403;
    }
}
