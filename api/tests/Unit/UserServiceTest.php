<?php

namespace Tests\Unit;

use App\Dtos\Users\UserFilterDto;
use App\Dtos\Users\UserStatusDto;
use App\Dtos\Users\UserUpdateDto;
use App\Exceptions\Auth\UnauthorizedActionException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Auth\AuthContext;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        Log::shouldReceive('info')->byDefault();
    }

    public function test_list_requires_admin(): void
    {
        $authContext = Mockery::mock(AuthContext::class);
        $userRepository = Mockery::mock(UserRepository::class);
        $service = new UserService($userRepository, $authContext);

        $authContext->shouldReceive('isAdmin')->once()->andReturn(false);

        $this->expectException(UnauthorizedActionException::class);

        $service->list(new UserFilterDto([]));
    }

    public function test_update_status_prevents_self_modification(): void
    {
        $authContext = Mockery::mock(AuthContext::class);
        $userRepository = Mockery::mock(UserRepository::class);
        $service = new UserService($userRepository, $authContext);

        $authContext->shouldReceive('isAdmin')->once()->andReturn(true);
        $authContext->shouldReceive('uuid')->andReturn('admin-uuid');

        $dto = new UserStatusDto(['uuid' => 'admin-uuid', 'is_active' => false]);

        $this->expectException(UnauthorizedActionException::class);

        $service->updateStatus($dto);
    }

    public function test_update_status_succeeds_for_different_user(): void
    {
        $authContext = Mockery::mock(AuthContext::class);
        $userRepository = Mockery::mock(UserRepository::class);
        $service = new UserService($userRepository, $authContext);

        $authContext->shouldReceive('isAdmin')->once()->andReturn(true);
        $authContext->shouldReceive('uuid')->andReturn('admin-uuid');
        $authContext->shouldReceive('id')->once()->andReturn(1);

        $user = new User();
        $user->forceFill([
            'id' => 99,
            'uuid' => 'target-uuid',
            'name' => 'Target',
            'email' => 'target@example.com',
            'is_active' => false,
        ]);

        $userRepository->shouldReceive('updateStatus')
            ->once()
            ->with(Mockery::on(function (UserStatusDto $dto) {
                return $dto->uuid === 'target-uuid' && $dto->is_active === false;
            }))
            ->andReturn($user);

        $result = $service->updateStatus(new UserStatusDto(['uuid' => 'target-uuid', 'is_active' => false]));

        $this->assertSame($user, $result);
    }

    public function test_update_user_calls_repository_and_logs(): void
    {
        $authContext = Mockery::mock(AuthContext::class);
        $userRepository = Mockery::mock(UserRepository::class);
        $service = new UserService($userRepository, $authContext);

        $authContext->shouldReceive('isAdmin')->once()->andReturn(true);
        $authContext->shouldReceive('id')->once()->andReturn(1);
        $authContext->shouldReceive('uuid')->andReturn('admin-uuid');

        $user = new User();
        $user->forceFill([
            'id' => 50,
            'uuid' => 'user-uuid',
            'name' => 'Updated',
            'email' => 'updated@example.com',
            'is_active' => true,
        ]);

        $userRepository->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function (UserUpdateDto $dto) {
                return $dto->uuid === 'user-uuid' && $dto->name === 'User Atualizado';
            }))
            ->andReturn($user);

        $result = $service->update(new UserUpdateDto([
            'uuid' => 'user-uuid',
            'name' => 'User Atualizado',
        ]));

        $this->assertSame($user, $result);
    }
}
