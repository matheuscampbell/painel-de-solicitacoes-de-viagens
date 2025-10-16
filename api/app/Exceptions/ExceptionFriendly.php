<?php

namespace App\Exceptions;

use Exception;

class ExceptionFriendly extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if(!is_integer($code)){
            $code = 500;
        }
        parent::__construct($message, $code, $previous);
    }

    public function getFriendlyMessage()
    {
        return "Oops! Algo deu errado. Aqui está o que sabemos: " . $this->getMessage();
    }
}
