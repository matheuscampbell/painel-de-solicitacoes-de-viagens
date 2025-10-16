<?php

namespace App\Exceptions\Auth;

use App\Exceptions\Base\BusinessException;

class UnauthorizedActionException extends BusinessException
{
    protected $message = 'Você não tem permissão para executar esta ação.';

    public function statusCode(): int
    {
        return 403;
    }
}

