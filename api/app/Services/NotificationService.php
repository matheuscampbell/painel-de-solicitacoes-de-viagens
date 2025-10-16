<?php

namespace App\Services;

use App\Dtos\Notifications\NotificationFilterDto;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository
    ) {
    }

    /**
     * @return Collection<int, DatabaseNotification>|LengthAwarePaginator
     */
    public function list(NotificationFilterDto $dto): Collection|LengthAwarePaginator
    {
        return $this->notificationRepository->list($dto);
    }

    public function markAsRead(string $notificationId): DatabaseNotification
    {
        return $this->notificationRepository->markAsRead($notificationId);
    }

    public function unreadCount(): int
    {
        return $this->notificationRepository->unreadCount();
    }
}
