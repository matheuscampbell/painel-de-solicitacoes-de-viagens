<?php

namespace App\Repositories;

use App\Dtos\Notifications\NotificationFilterDto;
use App\Exceptions\Notifications\NotificationNotFoundException;
use App\Services\Auth\AuthContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

class NotificationRepository
{
    public function __construct(
        private readonly AuthContext $authContext
    ) {
    }

    /**
     * @return Collection<int, DatabaseNotification>|LengthAwarePaginator
     */
    public function list(NotificationFilterDto $dto): Collection|LengthAwarePaginator
    {
        $user = $this->authContext->user();

        $query = $user->notifications()->orderByDesc('created_at');

        if ($dto->per_page) {
            return $query->paginate($dto->per_page)->withQueryString();
        }

        return $query->get();
    }

    /**
     * @throws NotificationNotFoundException
     */
    public function markAsRead(string $notificationId): DatabaseNotification
    {
        $user = $this->authContext->user();

        /** @var DatabaseNotification|null $notification */
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if (!$notification) {
            throw new NotificationNotFoundException();
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return $notification->fresh();
    }

    public function unreadCount(): int
    {
        $user = $this->authContext->user();

        return $user->unreadNotifications()->count();
    }
}
