<?php

namespace Database\Factories;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'creator_id' => User::factory()->state(['role' => 'admin']),
            'title' => fake()->words(3, true).' Cup',
            'slug' => fn (array $attributes): string => Str::slug($attributes['title']).'-'.Str::lower(Str::random(5)),
            'sport_type' => fake()->randomElement(['Football', 'Basketball', 'Esports', 'Cricket']),
            'status' => 'registration_open',
            'current_stage' => 'group',
            'banner' => null,
            'description' => fake()->paragraph(),
            'venue' => fake()->city().' Stadium',
            'start_date' => now()->addWeek()->toDateString(),
            'end_date' => now()->addWeeks(2)->toDateString(),
            'prize_pool' => '$10,000 USD',
            'max_teams' => 8,
        ];
    }
}
