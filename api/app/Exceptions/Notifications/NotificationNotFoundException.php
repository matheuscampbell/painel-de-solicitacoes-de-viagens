<?php

namespace App\Exceptions\Notifications;

use App\Exceptions\Base\BusinessException;

class NotificationNotFoundException extends BusinessException
{
    protected $message = 'Notificação não encontrada.';

    public function statusCode(): int
    {
        return 404;
    }
}

