<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ParticipationWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creator = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create(['role' => 'manager']);
        $player = User::factory()->create(['role' => 'player']);

        $team = Team::factory()->for($manager, 'manager')->create();
        $tournament = Tournament::factory()
            ->for($creator, 'creator')
            ->create(['sport_type' => $team->sport_type]);

        TeamJoinRequest::factory()
            ->for($team)
            ->for($player, 'player')
            ->create();

        TournamentInvitation::factory()
            ->forTeam($team)
            ->create([
                'tournament_id' => $tournament->id,
                'invited_by' => $creator->id,
            ]);
    }
}
