<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->word(3, true),
            'description' => fake()->paragraph(),
            'status' => $this->faker->randomElement(StatusEnum::cases())->value,
            'reproduction_step' => $this->faker->text(),
            'error_code' => $this->faker->randomAscii()
        ];
    }
}
