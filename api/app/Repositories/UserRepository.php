<?php

namespace App\Repositories;

use App\Dtos\Users\UserFilterDto;
use App\Dtos\Users\UserStoreDto;
use App\Dtos\Users\UserUpdateDto;
use App\Dtos\Users\UserStatusDto;
use App\Exceptions\Users\UserNotFoundException;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * @return Collection<int, User>|LengthAwarePaginator
     */
    public function list(UserFilterDto $dto): Collection|LengthAwarePaginator
    {
        $query = User::query();

        if ($dto->search) {
            $query->where(function ($q) use ($dto) {
                $q->where('name', 'like', '%' . $dto->search . '%')
                    ->orWhere('email', 'like', '%' . $dto->search . '%');
            });
        }

        if ($dto->tipo_usuario) {
            $query->where('tipo_usuario', $dto->tipo_usuario);
        }

        if ($dto->status) {
            $query->where('is_active', $dto->status === 'active');
        }

        $query->orderBy('name');

        if ($dto->per_page) {
            return $query->paginate($dto->per_page)->withQueryString();
        }

        return $query->get();
    }

    public function create(UserStoreDto $dto): User
    {
        $data = $dto->toArray();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $data['is_active'] ?? true;

        return User::create($data);
    }

    public function update(UserUpdateDto $dto): User
    {
        $user = User::where('uuid', $dto->uuid)->first();

        if (!$user) {
            throw new UserNotFoundException();
        }

        $data = $dto->toArray(skip: ['uuid'], skipIfNull: true);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

    public function updateStatus(UserStatusDto $dto): User
    {
        $user = User::where('uuid', $dto->uuid)->first();

        if (!$user) {
            throw new UserNotFoundException();
        }

        $user->is_active = $dto->is_active;
        $user->save();

        return $user;
    }
}
