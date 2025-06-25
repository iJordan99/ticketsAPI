<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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

        $types = ['incident', 'problem', 'question', 'request'];
        $priorities = array_keys(Ticket::priorityMap);
        return [
            'user_id' => User::factory(),
            'title' => fake()->word(3, true),
            'description' => fake()->paragraph(),
            'type' => Arr::random($types),
            'status' => $this->faker->randomElement(StatusEnum::cases())->value,
            'priority' => Arr::random($priorities),
            'reproduction_step' => $this->faker->text(),
            'error_code' => $this->faker->randomAscii()
        ];
    }
}
