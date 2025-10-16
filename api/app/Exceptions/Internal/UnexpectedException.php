<?php

namespace App\Exceptions\Internal;

use App\Exceptions\Base\DomainException;

class UnexpectedException extends DomainException
{
    public function statusCode(): int
    {
        return 500;
    }

    protected function getDefaultMessage(): string
    {
        return 'Ocorreu um erro inesperado. Tente novamente mais tarde.';
    }
}

