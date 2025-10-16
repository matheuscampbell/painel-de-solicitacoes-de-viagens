<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class AuthContext
{
    public function __construct(
        private readonly AuthFactory $auth
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function user(): User
    {
        $user = $this->auth->guard('api')->user() ?? $this->auth->guard()->user();

        if (!$user instanceof User) {
            throw new AuthenticationException('Usuário não autenticado.');
        }

        return $user;
    }

    /**
     * @throws AuthenticationException
     */
    public function id(): int
    {
        return $this->user()->id;
    }

    /**
     * @throws AuthenticationException
     */
    public function uuid(): string
    {
        return $this->user()->uuid;
    }

    /**
     * @throws AuthenticationException
     */
    public function isAdmin(): bool
    {
        return $this->user()->isAdmin();
    }
}
