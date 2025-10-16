<?php

namespace App\Notifications;

use App\Enums\TravelOrderStatus;
use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TravelOrderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected TravelOrder $travelOrder,
        protected TravelOrderStatus $previousStatus
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'travel_order_uuid' => $this->travelOrder->uuid,
            'destination' => $this->travelOrder->destination,
            'status' => $this->travelOrder->status->value,
            'previous_status' => $this->previousStatus->value,
            'message' => $this->message(),
        ];
    }

    protected function message(): string
    {
        $status = $this->travelOrder->status->label();

        return sprintf(
            'Seu pedido de viagem para %s foi %s.',
            $this->travelOrder->destination,
            strtolower($status)
        );
    }
}
