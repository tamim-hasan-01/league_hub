<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class  DemoAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_displays_ _evaluation_showcase(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('  Evaluation Access');
        $response->assertSee('Prof. Demo (Manager)');
        $response->assertSee('Prof. Demo (Player)');
        $response->assertSee('Prof. Demo (Viewer)');
        $response->assertSee('No password required');
    }

    public function test_other_pages_do_not_display_ _demo_login_buttons(): void
    {
        // Home page should not have demo login buttons
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertDontSee('  Evaluation Access');

        // Register page should not have demo login buttons
        $registerResponse = $this->get('/register');
        $registerResponse->assertStatus(200);
        $registerResponse->assertDontSee('  Evaluation Access');

        // About page should not have demo login buttons
        $aboutResponse = $this->get('/about');
        $aboutResponse->assertStatus(200);
        $aboutResponse->assertDontSee('  Evaluation Access');
    }

    public function test_ _manager_can_sign_in_without_password(): void
    {
        $response = $this->post('/quick-login', [
            'role' => 'manager',
        ]);

        $response->assertRedirect('/mytournaments');
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('prof.manager@leaguehub.com', $user->email);
        $this->assertTrue($user->isManager());
    }

    public function test_ _player_can_sign_in_without_password(): void
    {
        $response = $this->post('/quick-login', [
            'role' => 'player',
        ]);

        $response->assertRedirect('/mytournaments');
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('prof.player@leaguehub.com', $user->email);
        $this->assertTrue($user->isPlayer());
        $this->assertNotNull($user->playerProfile);
    }

    public function test_ _viewer_can_sign_in_without_password(): void
    {
        $response = $this->post('/quick-login', [
            'role' => 'viewer',
        ]);

        $response->assertRedirect('/mytournaments');
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('prof.viewer@leaguehub.com', $user->email);
        $this->assertTrue($user->isViewer());
    }

    public function test_standard_login_form_allows_demo_account_without_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'prof.manager@leaguehub.com',
            'password' => '',
            'role' => 'manager',
        ]);

        $response->assertRedirect('/mytournaments');
        $this->assertAuthenticated();
        $this->assertEquals('prof.manager@leaguehub.com', auth()->user()->email);
    }

    public function test_demo_accounts_have_rich_hub_data(): void
    {
        // 1. Manager Hub
        $manager = User::where('email', 'prof.manager@leaguehub.com')->first();
        $this->assertNotNull($manager);
        $managerHubResp = $this->actingAs($manager)->get('/mytournaments');
        $managerHubResp->assertStatus(200);
        $managerHubResp->assertSee('  Titans FC');

        // 2. Player Hub
        $player = User::where('email', 'prof.player@leaguehub.com')->first();
        $this->assertNotNull($player);
        $playerHubResp = $this->actingAs($player)->get('/mytournaments');
        $playerHubResp->assertStatus(200);
        $playerHubResp->assertSee('Awaiting response');

        // 3. Player Stats
        $playerStatsResp = $this->actingAs($player)->get('/mystats');
        $playerStatsResp->assertStatus(200);
        $playerStatsResp->assertSee('Form Curve');

        // 4. Viewer Hub
        $viewer = User::where('email', 'prof.viewer@leaguehub.com')->first();
        $this->assertNotNull($viewer);
        $viewerHubResp = $this->actingAs($viewer)->get('/mytournaments');
        $viewerHubResp->assertStatus(200);
        $viewerHubResp->assertSee('Bookmarks and following');
    }
}
