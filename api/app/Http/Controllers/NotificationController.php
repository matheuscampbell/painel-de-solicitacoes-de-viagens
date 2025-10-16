<?php

namespace App\Http\Controllers;

use App\Exceptions\Base\DomainException;
use App\Http\Requests\Notifications\NotificationIndexRequest;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly NotificationService $notificationService
    ) {
        $this->middleware('auth:api');
    }

    public function index(NotificationIndexRequest $request): JsonResponse
    {
        try {
            $notifications = $this->notificationService->list($request->toDto());

            if ($notifications instanceof LengthAwarePaginator) {
                return $this->paginatedSuccess(
                    $notifications,
                    NotificationResource::collection($notifications->getCollection()),
                    'Notificações listadas com sucesso.'
                );
            }

            return $this->success(
                NotificationResource::collection($notifications),
                200,
                'Notificações listadas com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao listar notificações.');
        }
    }

    public function markAsRead(string $notification): JsonResponse
    {
        try {
            $notificationModel = $this->notificationService->markAsRead($notification);

            return $this->success(
                new NotificationResource($notificationModel),
                200,
                'Notificação marcada como lida com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao marcar notificação como lida.');
        }
    }

    public function unreadCount(): JsonResponse
    {
        try {
            $count = $this->notificationService->unreadCount();

            return $this->success(
                ['unread_count' => $count],
                200,
                'Contagem de notificações não lidas recuperada com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao contar notificações não lidas.');
        }
    }

    private function handleException(Throwable $exception, string $logMessage): JsonResponse
    {
        if ($exception instanceof DomainException) {
            return $this->error($exception->getMessage(), $exception->statusCode());
        }

        Log::error($logMessage, [
            'exception' => $exception,
        ]);

        return $this->error('Ocorreu um erro interno. Tente novamente mais tarde.', 500);
    }
}
