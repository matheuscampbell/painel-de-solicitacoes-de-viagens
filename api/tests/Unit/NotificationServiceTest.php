<?php

namespace Tests\Unit;

use App\Dtos\Notifications\NotificationFilterDto;
use App\Repositories\NotificationRepository;
use App\Services\NotificationService;
use Illuminate\Notifications\DatabaseNotification;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_list_delegates_to_repository(): void
    {
        $repository = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($repository);

        $dto = new NotificationFilterDto(['per_page' => 10]);

        $empty = new \Illuminate\Database\Eloquent\Collection();

        $repository->shouldReceive('list')
            ->once()
            ->with($dto)
            ->andReturn($empty);

        $result = $service->list($dto);

        $this->assertSame($empty, $result);
    }

    public function test_mark_as_read_returns_notification(): void
    {
        $repository = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($repository);

        $notification = Mockery::mock(DatabaseNotification::class);

        $repository->shouldReceive('markAsRead')
            ->once()
            ->with('notification-id')
            ->andReturn($notification);

        $result = $service->markAsRead('notification-id');

        $this->assertSame($notification, $result);
    }

    public function test_unread_count_delegates_to_repository(): void
    {
        $repository = Mockery::mock(NotificationRepository::class);
        $service = new NotificationService($repository);

        $repository->shouldReceive('unreadCount')
            ->once()
            ->andReturn(5);

        $this->assertEquals(5, $service->unreadCount());
    }
}
