<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'manager_id' => User::factory()->state(['role' => 'manager']),
            'name' => fake()->company().' FC',
            'slug' => fn (array $attributes): string => Str::slug($attributes['name']).'-'.Str::lower(Str::random(5)),
            'logo' => null,
            'sport_type' => fake()->randomElement(['Football', 'Basketball', 'Esports', 'Cricket']),
            'areal_ranking' => fake()->numberBetween(1, 1500),
            'description' => fake()->sentence(),
            'home_venue' => fake()->city().' Arena',
        ];
    }
}
