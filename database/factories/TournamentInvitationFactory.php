<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TournamentInvitation>
 */
class TournamentInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'manager_id' => User::factory()->state(['role' => 'manager']),
            'team_id' => null,
            'target_type' => 'manager',
            'target_id' => 1,
            'invited_by' => User::factory()->state(['role' => 'admin']),
            'status' => 'pending',
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (TournamentInvitation $invitation): void {
            $invitation->target_id = $invitation->target_type === 'team'
                ? $invitation->team_id
                : $invitation->manager_id;
        });
    }

    public function forTeam(Team $team): static
    {
        return $this->state(fn (): array => [
            'manager_id' => $team->manager_id,
            'team_id' => $team->id,
            'target_type' => 'team',
            'target_id' => $team->id,
        ]);
    }

    public function forManager(User $manager): static
    {
        return $this->state(fn (): array => [
            'manager_id' => $manager->id,
            'team_id' => null,
            'target_type' => 'manager',
            'target_id' => $manager->id,
        ]);
    }
}
