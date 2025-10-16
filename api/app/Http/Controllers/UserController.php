<?php

namespace App\Http\Controllers;

use App\Exceptions\Base\DomainException;
use App\Http\Requests\Users\UserIndexRequest;
use App\Http\Requests\Users\UserStatusUpdateRequest;
use App\Http\Requests\Users\UserStoreRequest;
use App\Http\Requests\Users\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserService $userService
    )
    {
        $this->middleware('auth:api');
    }

    public function index(UserIndexRequest $request): JsonResponse
    {
        try {
            $users = $this->userService->list($request->toDto());

            if ($users instanceof LengthAwarePaginator) {
                return $this->paginatedSuccess(
                    $users,
                    UserResource::collection($users->getCollection()),
                    'Usuários listados com sucesso.'
                );
            }

            return $this->success(
                UserResource::collection($users),
                200,
                'Usuários listados com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao listar usuários.');
        }
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->create($request->toDto());

            return $this->success(
                new UserResource($user),
                201,
                'Usuário criado com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao criar usuário.');
        }
    }

    public function updateStatus(UserStatusUpdateRequest $request, string $user): JsonResponse
    {
        try {
            $userModel = $this->userService->updateStatus($request->toDto($user));

            $message = $userModel->is_active ? 'Usuário ativado com sucesso.' : 'Usuário desativado com sucesso.';

            return $this->success(
                new UserResource($userModel),
                200,
                $message
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao atualizar status do usuário.');
        }
    }

    public function update(UserUpdateRequest $request, string $user): JsonResponse
    {
        try {
            $userModel = $this->userService->update($request->toDto($user));

            return $this->success(
                new UserResource($userModel),
                200,
                'Usuário atualizado com sucesso.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'Erro ao atualizar usuário.');
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
