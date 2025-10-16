<?php

namespace App\Exceptions\External;

use App\Exceptions\Base\DomainException;

class ExternalServiceException extends DomainException
{
    protected string $serviceName;

    public function __construct(string $serviceName, string $message = '', int $code = 0, ?\Exception $previous = null)
    {
        $this->serviceName = $serviceName;
        parent::__construct($message ?: $this->getDefaultMessage(), $code, $previous);
    }

    public function statusCode(): int
    {
        return 502;
    }

    protected function getDefaultMessage(): string
    {
        return "Erro ao se comunicar com o serviço externo {$this->serviceName}.";
    }
}

