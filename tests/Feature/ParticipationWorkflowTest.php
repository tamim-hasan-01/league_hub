<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use App\Models\TournamentInvitation;
use App\Models\User;
use App\Notifications\TournamentApplicationStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_tournament_creator_can_find_and_send_team_invitations(): void
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $teamManager = User::factory()->create(['role' => 'manager']);
        $tournament = Tournament::factory()->create([
            'creator_id' => $creator->id,
            'sport_type' => 'Football',
        ]);
        $team = Team::factory()->create([
            'manager_id' => $teamManager->id,
            'sport_type' => 'Football',
        ]);

        $page = $this->actingAs($creator)->get(route('tournaments.show', $tournament->slug));

        $page->assertOk();
        $page->assertSee('Invite teams or managers');
        $page->assertSee('Send team invitation');

        $response = $this->actingAs($creator)->post(route('tournaments.inviteTeam', $tournament), [
            'team_id' => $team->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tournament_invitations', [
            'tournament_id' => $tournament->id,
            'manager_id' => $teamManager->id,
            'team_id' => $team->id,
            'target_type' => 'team',
            'target_id' => $team->id,
            'status' => 'pending',
        ]);
    }

    public function test_invited_team_manager_can_accept_and_register_the_team(): void
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $manager = User::factory()->create(['role' => 'manager']);
        $tournament = Tournament::factory()->create([
            'creator_id' => $creator->id,
            'sport_type' => 'Football',
        ]);
        $team = Team::factory()->create([
            'manager_id' => $manager->id,
            'sport_type' => 'Football',
        ]);
        $invitation = TournamentInvitation::factory()
            ->forTeam($team)
            ->create([
                'tournament_id' => $tournament->id,
                'invited_by' => $creator->id,
            ]);

        $response = $this->actingAs($manager)->post(route('tournament-invitations.respond', $invitation), [
            'status' => 'accepted',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tournament_invitations', [
            'id' => $invitation->id,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('tournament_applications', [
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'status' => 'approved',
        ]);
    }

    public function test_invited_manager_can_select_a_team_and_accept(): void
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $manager = User::factory()->create(['role' => 'manager']);
        $tournament = Tournament::factory()->create([
            'creator_id' => $creator->id,
            'sport_type' => 'Basketball',
        ]);
        $team = Team::factory()->create([
            'manager_id' => $manager->id,
            'sport_type' => 'Basketball',
        ]);
        $invitation = TournamentInvitation::factory()
            ->forManager($manager)
            ->create([
                'tournament_id' => $tournament->id,
                'invited_by' => $creator->id,
            ]);

        $hub = $this->actingAs($manager)->get(route('mytournaments'));

        $hub->assertOk();
        $hub->assertSee($tournament->title);
        $hub->assertSee('Accept invite');

        $response = $this->actingAs($manager)->post(route('tournament-invitations.respond', $invitation), [
            'status' => 'accepted',
            'team_id' => $team->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tournament_invitations', [
            'id' => $invitation->id,
            'team_id' => $team->id,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('tournament_applications', [
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'status' => 'approved',
        ]);
    }

    public function test_player_can_request_team_membership_and_manager_can_approve_it(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $player = User::factory()->create(['role' => 'player']);
        $team = Team::factory()->create(['manager_id' => $manager->id]);

        $teamPage = $this->actingAs($player)->get(route('teams.show', $team->slug));

        $teamPage->assertOk();
        $teamPage->assertSee('Send team request');

        $requestResponse = $this->actingAs($player)->post(route('teams.joinRequests.store', $team), [
            'position' => 'Midfielder',
            'notes' => 'Available for the upcoming season.',
        ]);

        $requestResponse->assertRedirect();
        $this->assertDatabaseHas('team_join_requests', [
            'team_id' => $team->id,
            'player_id' => $player->id,
            'position' => 'Midfielder',
            'status' => 'pending',
        ]);

        $joinRequest = TeamJoinRequest::where('team_id', $team->id)
            ->where('player_id', $player->id)
            ->firstOrFail();
        $hub = $this->actingAs($manager)->get(route('mytournaments'));
        $hub->assertOk();
        $hub->assertSee($player->name);

        $approvalResponse = $this->actingAs($manager)->post(route('team-join-requests.respond', $joinRequest), [
            'status' => 'approved',
        ]);

        $approvalResponse->assertRedirect();
        $this->assertDatabaseHas('team_join_requests', [
            'id' => $joinRequest->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $player->id,
            'position' => 'Midfielder',
            'status' => 'active',
        ]);
    }

    public function test_non_creator_cannot_invite_teams_to_a_tournament(): void
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $otherManager = User::factory()->create(['role' => 'manager']);
        $team = Team::factory()->create(['manager_id' => User::factory()->create(['role' => 'manager'])->id]);
        $tournament = Tournament::factory()->create([
            'creator_id' => $creator->id,
            'sport_type' => $team->sport_type,
        ]);

        $response = $this->actingAs($otherManager)->post(route('tournaments.inviteTeam', $tournament), [
            'team_id' => $team->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('tournament_invitations', [
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
        ]);
    }

    public function test_non_creator_cannot_review_tournament_applications(): void
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $otherManager = User::factory()->create(['role' => 'manager']);
        $teamManager = User::factory()->create(['role' => 'manager']);
        $tournament = Tournament::factory()->create(['creator_id' => $creator->id]);
        $team = Team::factory()->create(['manager_id' => $teamManager->id]);
        $application = TournamentApplication::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($otherManager)->post(
            route('tournaments.applications.review', [$tournament, $application]),
            ['status' => 'approved'],
        );

        $response->assertForbidden();
        $this->assertDatabaseHas('tournament_applications', [
            'id' => $application->id,
            'status' => 'pending',
        ]);
    }

    public function test_only_the_invited_manager_can_respond_to_a_team_invitation(): void
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $invitedManager = User::factory()->create(['role' => 'manager']);
        $otherManager = User::factory()->create(['role' => 'manager']);
        $tournament = Tournament::factory()->create(['creator_id' => $creator->id]);
        $team = Team::factory()->create([
            'manager_id' => $invitedManager->id,
            'sport_type' => $tournament->sport_type,
        ]);
        $invitation = TournamentInvitation::factory()
            ->forTeam($team)
            ->create([
                'tournament_id' => $tournament->id,
                'invited_by' => $creator->id,
            ]);

        $response = $this->actingAs($otherManager)->post(route('tournament-invitations.respond', $invitation), [
            'status' => 'declined',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('tournament_invitations', [
            'id' => $invitation->id,
            'status' => 'pending',
        ]);
    }

    public function test_manager_cannot_approve_a_player_request_for_another_team(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $otherManager = User::factory()->create(['role' => 'manager']);
        $player = User::factory()->create(['role' => 'player']);
        $team = Team::factory()->create(['manager_id' => $manager->id]);
        $joinRequest = TeamJoinRequest::factory()
            ->for($team)
            ->for($player, 'player')
            ->create();

        $response = $this->actingAs($otherManager)->post(route('team-join-requests.respond', $joinRequest), [
            'status' => 'approved',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('team_join_requests', [
            'id' => $joinRequest->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $player->id,
        ]);
    }

    public function test_non_player_cannot_request_team_membership(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $team = Team::factory()->create(['manager_id' => User::factory()->create(['role' => 'manager'])->id]);

        $response = $this->actingAs($manager)->post(route('teams.joinRequests.store', $team), [
            'position' => 'Forward',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('team_join_requests', [
            'team_id' => $team->id,
            'player_id' => $manager->id,
        ]);
    }

    public function test_manager_cannot_accept_an_invitation_with_another_managers_team(): void
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $invitedManager = User::factory()->create(['role' => 'manager']);
        $otherManager = User::factory()->create(['role' => 'manager']);
        $tournament = Tournament::factory()->create([
            'creator_id' => $creator->id,
            'sport_type' => 'Football',
        ]);
        $team = Team::factory()->create([
            'manager_id' => $otherManager->id,
            'sport_type' => 'Football',
        ]);
        $invitation = TournamentInvitation::factory()
            ->forManager($invitedManager)
            ->create([
                'tournament_id' => $tournament->id,
                'invited_by' => $creator->id,
            ]);

        $response = $this->actingAs($invitedManager)->post(route('tournament-invitations.respond', $invitation), [
            'status' => 'accepted',
            'team_id' => $team->id,
        ]);

        $response->assertRedirectBackWithErrors(['team_id']);
        $this->assertDatabaseHas('tournament_invitations', [
            'id' => $invitation->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('tournament_applications', [
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
        ]);
    }

    public function test_tournament_creator_can_review_applications_and_notify_the_manager(): void
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $manager = User::factory()->create(['role' => 'manager']);
        $tournament = Tournament::factory()->create([
            'creator_id' => $creator->id,
            'sport_type' => 'Football',
        ]);
        $team = Team::factory()->create([
            'manager_id' => $manager->id,
            'sport_type' => 'Football',
        ]);
        $application = TournamentApplication::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($creator)->post(
            route('tournaments.applications.review', [$tournament, $application]),
            ['status' => 'approved'],
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('tournament_applications', [
            'id' => $application->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $manager->id,
            'type' => TournamentApplicationStatusChanged::class,
        ]);
    }

    public function test_invitation_acceptance_does_not_exceed_tournament_team_limit(): void
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $manager = User::factory()->create(['role' => 'manager']);
        $existingManager = User::factory()->create(['role' => 'manager']);
        $tournament = Tournament::factory()->create([
            'creator_id' => $creator->id,
            'sport_type' => 'Football',
            'max_teams' => 1,
        ]);
        $existingTeam = Team::factory()->create([
            'manager_id' => $existingManager->id,
            'sport_type' => 'Football',
        ]);
        $team = Team::factory()->create([
            'manager_id' => $manager->id,
            'sport_type' => 'Football',
        ]);
        TournamentApplication::create([
            'tournament_id' => $tournament->id,
            'team_id' => $existingTeam->id,
            'status' => 'approved',
        ]);
        $invitation = TournamentInvitation::factory()
            ->forTeam($team)
            ->create([
                'tournament_id' => $tournament->id,
                'invited_by' => $creator->id,
            ]);

        $response = $this->actingAs($manager)->post(route('tournament-invitations.respond', $invitation), [
            'status' => 'accepted',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'This tournament has reached its team limit.');
        $this->assertDatabaseHas('tournament_invitations', [
            'id' => $invitation->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('tournament_applications', [
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
        ]);
    }
}
