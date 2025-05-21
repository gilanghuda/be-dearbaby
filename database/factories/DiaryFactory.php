<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diary>
 */
class DiaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message' => $this->faker->sentence(10),
            'moodcheck' => $this->faker->randomElement(['1', '2', '3', '4', '5', '6']),
            'user_id' => 1, // atau generate user_id yang valid
            'created_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
