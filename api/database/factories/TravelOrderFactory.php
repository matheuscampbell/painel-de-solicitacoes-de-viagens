<?php

namespace Database\Factories;

use App\Enums\TravelOrderStatus;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TravelOrder>
 */
class TravelOrderFactory extends Factory
{
    protected $model = TravelOrder::class;

    public function definition(): array
    {
        $departure = fake()->dateTimeBetween('+1 day', '+1 month');
        $return = (clone $departure)->modify('+' . fake()->numberBetween(2, 10) . ' days');

        return [
            'user_id' => User::factory(),
            'uuid' => (string) Str::uuid(),
            'origin' => fake()->city(),
            'destination' => fake()->city(),
            'departure_date' => $departure->format('Y-m-d'),
            'return_date' => $return->format('Y-m-d'),
            'status' => TravelOrderStatus::SOLICITADO,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function approved(): self
    {
        return $this->state(fn () => ['status' => TravelOrderStatus::APROVADO]);
    }

    public function cancelled(): self
    {
        return $this->state(fn () => ['status' => TravelOrderStatus::CANCELADO]);
    }
}
