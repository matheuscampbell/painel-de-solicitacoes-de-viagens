<?php

namespace App\Exceptions\Base;

use Exception;

abstract class DomainException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message ?: $this->getDefaultMessage(), $code ?: $this->statusCode(), $previous);
    }

    abstract public function statusCode(): int;

    protected function getDefaultMessage(): string
    {
        return 'Ocorreu um erro durante a requisição.';
    }
}

