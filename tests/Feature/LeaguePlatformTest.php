<?php

namespace Tests\Feature;

use App\Models\GameMatch;
use App\Models\Tournament;
use App\Models\User;
use App\Services\TournamentBracketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaguePlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('LEAGUEHUB');
        $response->assertSee('Match ticker');
    }

    public function test_tournaments_index_page_loads(): void
    {
        $response = $this->get('/tournaments');
        $response->assertStatus(200);
        $response->assertSee('Tournaments');
    }

    public function test_admin_can_open_the_tournament_creation_form(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/tournaments');

        $response->assertStatus(200);
        $response->assertSee('name="title"', false);
        $response->assertSee('action="'.route('tournaments.store').'"', false);
    }

    public function test_admin_can_create_a_tournament(): void
    {
        $admin = User::where('role', 'admin')->first();
        $tournamentData = [
            'title' => 'Community Cup',
            'sport_type' => 'Football',
            'description' => 'A local football competition.',
            'venue' => 'Central Stadium',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-10',
            'prize_pool' => 'Championship trophy',
            'max_teams' => 16,
        ];

        $response = $this->actingAs($admin)->post(route('tournaments.store'), $tournamentData);

        $tournament = Tournament::where('title', 'Community Cup')->firstOrFail();
        $response->assertRedirect(route('tournaments.show', $tournament->slug));
        $this->assertDatabaseHas('tournaments', [
            'title' => 'Community Cup',
            'creator_id' => $admin->id,
            'sport_type' => 'Football',
            'venue' => 'Central Stadium',
            'max_teams' => 16,
            'status' => 'registration_open',
        ]);
    }

    public function test_non_admin_cannot_create_a_tournament(): void
    {
        $manager = User::where('role', 'manager')->first();

        $response = $this->actingAs($manager)->post(route('tournaments.store'), [
            'title' => 'Unauthorized Cup',
            'sport_type' => 'Football',
            'max_teams' => 8,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('tournaments', [
            'title' => 'Unauthorized Cup',
        ]);
    }

    public function test_guest_is_redirected_to_sign_in_before_creating_a_tournament(): void
    {
        $response = $this->post(route('tournaments.store'), [
            'title' => 'Guest Cup',
            'sport_type' => 'Football',
            'max_teams' => 8,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('tournaments', [
            'title' => 'Guest Cup',
        ]);
    }

    public function test_teams_directory_loads(): void
    {
        $response = $this->get('/teams');
        $response->assertStatus(200);
        $response->assertSee('Teams');
    }

    public function test_about_page_loads(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('League operations');
    }

    public function test_news_page_loads(): void
    {
        $response = $this->get('/news');
        $response->assertStatus(200);
        $response->assertSee('News and incidents');
    }

    public function test_live_ticker_api_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/matches/ticker');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'updated_at',
            'matches' => [
                '*' => ['id', 'home_team', 'away_team', 'home_score', 'away_score', 'status'],
            ],
        ]);
    }

    public function test_admin_dashboard_is_guarded_against_unauthorized_users(): void
    {
        // Guest redirect to login
        $guestResp = $this->get('/admin/dashboard');
        $guestResp->assertRedirect('/login');

        // Viewer should receive 403 Forbidden
        $viewer = User::where('role', 'viewer')->first();
        $this->assertNotNull($viewer);
        $viewerResp = $this->actingAs($viewer)->get('/admin/dashboard');
        $viewerResp->assertStatus(403);

        // Admin should receive 200 OK
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);
        $adminResp = $this->actingAs($admin)->get('/admin/dashboard');
        $adminResp->assertStatus(200);
        $adminResp->assertSee('League Authority');
    }

    public function test_admin_personal_hub_loads_the_member_directory(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('mytournaments'));

        $response->assertStatus(200);
        $response->assertViewHas('memberDirectory');
    }

    public function test_player_analytics_page_accessible_by_player(): void
    {
        $player = User::where('role', 'player')->first();
        $this->assertNotNull($player);

        $response = $this->actingAs($player)->get('/mystats');
        $response->assertStatus(200);
        $response->assertSee('Form Curve');
    }

    public function test_player_can_toggle_health_status(): void
    {
        $player = User::where('role', 'player')->first();
        $this->assertNotNull($player);

        $response = $this->actingAs($player)->post('/mystats/health', [
            'is_injured' => 1,
            'injury_notes' => 'Hamstring tweak during drill practice',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('player_profiles', [
            'user_id' => $player->id,
            'is_injured' => true,
            'injury_notes' => 'Hamstring tweak during drill practice',
        ]);
    }

    public function test_admin_can_update_live_scores(): void
    {
        $admin = User::where('role', 'admin')->first();
        $match = GameMatch::first();
        $this->assertNotNull($admin);
        $this->assertNotNull($match);

        $response = $this->actingAs($admin)->post('/admin/matches/'.$match->id.'/score', [
            'home_score' => 4,
            'away_score' => 2,
            'status' => 'live',
            'event_type' => 'score',
            'event_description' => 'Brilliant header goal in bottom right corner',
        ]);

        $response->assertRedirect();
        $match->refresh();
        $this->assertEquals(4, $match->home_score);
        $this->assertEquals(2, $match->away_score);
    }

    public function test_bracket_service_seeds_fixtures_properly(): void
    {
        $tournament = Tournament::where('slug', 'apex-continental-championship-2026')->first();
        $this->assertNotNull($tournament);

        $service = new TournamentBracketService;
        $tree = $service->getBracketTree($tournament);

        $this->assertArrayHasKey('quarter_final', $tree);
        $this->assertArrayHasKey('semi_final', $tree);
        $this->assertArrayHasKey('final', $tree);
    }
}
