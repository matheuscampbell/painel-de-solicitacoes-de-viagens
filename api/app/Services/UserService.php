<?php

namespace App\Services;

use App\Dtos\Users\UserFilterDto;
use App\Dtos\Users\UserStatusDto;
use App\Dtos\Users\UserStoreDto;
use App\Dtos\Users\UserUpdateDto;
use App\Exceptions\Auth\UnauthorizedActionException;
use App\Repositories\UserRepository;
use App\Services\Auth\AuthContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthContext $authContext
    ) {
    }

    /**
     * @return Collection<int, \App\Models\User>|LengthAwarePaginator
     */
    public function list(UserFilterDto $dto): Collection|LengthAwarePaginator
    {
        $this->ensureAdmin();

        return $this->userRepository->list($dto);
    }

    public function create(UserStoreDto $dto)
    {
        $this->ensureAdmin();

        $user = $this->userRepository->create($dto);

        Log::info('Usuário criado via painel.', [
            'created_by' => $this->authContext->id(),
            'created_by_uuid' => $this->authContext->uuid(),
            'user_id' => $user->id,
            'user_uuid' => $user->uuid,
            'user_email' => $user->email,
        ]);

        return $user;
    }

    public function update(UserUpdateDto $dto)
    {
        $this->ensureAdmin();

        $user = $this->userRepository->update($dto);

        Log::info('Usuário atualizado via painel.', [
            'updated_by' => $this->authContext->id(),
            'updated_by_uuid' => $this->authContext->uuid(),
            'user_id' => $user->id,
            'user_uuid' => $user->uuid,
        ]);

        return $user;
    }

    public function updateStatus(UserStatusDto $dto)
    {
        $this->ensureAdmin();

        if ($dto->uuid === $this->authContext->uuid()) {
            throw new UnauthorizedActionException('Você não pode alterar o próprio status.');
        }

        $user = $this->userRepository->updateStatus($dto);

        Log::info('Status de usuário atualizado.', [
            'updated_by' => $this->authContext->id(),
            'updated_by_uuid' => $this->authContext->uuid(),
            'user_id' => $user->id,
            'user_uuid' => $user->uuid,
            'is_active' => $user->is_active,
        ]);

        return $user;
    }

    private function ensureAdmin(): void
    {
        if (!$this->authContext->isAdmin()) {
            throw new UnauthorizedActionException();
        }
    }
}
