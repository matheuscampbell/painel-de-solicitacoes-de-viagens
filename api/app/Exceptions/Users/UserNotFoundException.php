<?php

namespace App\Exceptions\Users;

use App\Exceptions\Base\BusinessException;

class UserNotFoundException extends BusinessException
{
    protected $message = 'Usuário não encontrado.';

    public function statusCode(): int
    {
        return 404;
    }
}

