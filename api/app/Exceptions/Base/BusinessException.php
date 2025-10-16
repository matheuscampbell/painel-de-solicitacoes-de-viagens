<?php

namespace App\Exceptions\Base;

abstract class BusinessException extends DomainException
{
    protected function getDefaultMessage(): string
    {
        return $this->message ?: parent::getDefaultMessage();
    }
}
