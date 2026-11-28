<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamJoinRequest>
 */
class TeamJoinRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'player_id' => User::factory()->state(['role' => 'player']),
            'position' => fake()->randomElement(['Forward', 'Midfielder', 'Defender', 'Goalkeeper']),
            'notes' => fake()->optional()->sentence(),
            'status' => 'pending',
            'responded_at' => null,
        ];
    }
}
