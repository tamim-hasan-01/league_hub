<?php

use App\Models\NewsArticle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $this->seed();
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Competition workspace');
    $response->assertDontSee('Toggle Theme');
    $response->assertSee('logo.png');
});

test('the about page presents the project and development team', function () {
    $response = $this->get('/about');

    $response->assertOk();
    $response->assertSee('Md. Tamim Hasan Siam');
    $response->assertSee('Muhammad Mobasher Ahmmed Milon');
    $response->assertSee('Waliur Rafiq Sami');
    $response->assertSee('0802410205101088');
    $response->assertSee('dev/sami.png');
    $response->assertSee('dev/mobahir.jpg');
    $response->assertSee('Search Facebook');
    $response->assertSee('Everything needed to run a competition');
    $response->assertSee('Built by BAUST CSE students');
});

test('the team directory presents team profiles', function () {
    $this->seed();
    $response = $this->get('/teams');

    $response->assertOk();
    $response->assertSee('Club directory');
    $response->assertSee('CyberPulse Esports');
});

test('the team detail page presents roster and match sections', function () {
    $this->seed();
    $response = $this->get('/teams/cyberpulse-esports');

    $response->assertOk();
    $response->assertSee('CyberPulse Esports');
    $response->assertSee('Roster and player status');
    $response->assertSee('Matches and results');
});

test('the news directory and article detail pages render', function () {
    $this->seed();

    $directory = $this->get('/news');
    $directory->assertOk();
    $directory->assertSee('League newsroom');

    $article = NewsArticle::query()->first();
    $this->assertNotNull($article);

    $detail = $this->get('/news/'.$article->slug);
    $detail->assertOk();
    $detail->assertSee($article->title);
});

test('an authenticated user can open the personal tournament hub', function () {
    $this->seed();
    $user = User::where('email', 'viewer@leaguehub.com')->first();

    $response = $this->actingAs($user)->get('/mytournaments');

    $response->assertOk();
    $response->assertSee('Personal hub');
    $response->assertSee('Bookmarks and following');
});

test('the tournament detail page presents competition sections', function () {
    $this->seed();
    $response = $this->get('/tournaments/apex-continental-championship-2026');

    $response->assertOk();
    $response->assertSee('Apex Continental Championship 2026');
    $response->assertSee('Tournament overview');
    $response->assertSee('Stage progression');
    $response->assertSee('Approved team rosters');
});

test('authentication pages render professional account forms', function () {
    $login = $this->get('/login');
    $login->assertOk();
    $login->assertSee('Sign in to LeagueHub');
    $login->assertSee('Choose an account type');

    $register = $this->get('/register');
    $register->assertOk();
    $register->assertSee('Create an account');
    $register->assertSee('Select your role');
    $login->assertDontSee('/demo-login');
});

test('only the three verified administrators can sign in through admin verification', function () {
    $this->seed();

    $this->post('/admin-login', [
        'admin_id' => 'sami',
        'password' => '123123123',
    ])->assertRedirect('/admin/dashboard');

    auth()->logout();

    $this->post('/admin-login', [
        'admin_id' => 'siam',
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('password');

    $this->post('/admin-login', [
        'admin_id' => 'unknown',
        'password' => '123123123',
    ])->assertSessionHasErrors('admin_id');
});

test('a player can register and persist a player profile', function () {
    $this->post('/register', [
        'name' => 'Test Player',
        'email' => 'test.player@example.com',
        'password' => 'StrongPass123!',
        'password_confirmation' => 'StrongPass123!',
        'role' => 'player',
        'areal_zone' => 'North Region',
        'primary_sport' => 'Football',
        'jersey_number' => 27,
    ])->assertRedirect('/');

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Test Player',
        'email' => 'test.player@example.com',
        'role' => 'player',
    ]);
    $this->assertDatabaseHas('player_profiles', [
        'jersey_number' => '27',
        'primary_sport' => 'Football',
    ]);
});

test('manager and viewer accounts can register and sign in with the saved password', function () {
    foreach ([
        'manager' => 'manager.account@example.com',
        'viewer' => 'viewer.account@example.com',
    ] as $role => $email) {
        auth()->logout();
        $this->post('/register', [
            'name' => ucfirst($role).' Account',
            'email' => $email,
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'role' => $role,
            'areal_zone' => 'Metro Central',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        auth()->logout();
    }

    $response = $this->post('/login', [
        'email' => 'manager.account@example.com',
        'password' => 'StrongPass123!',
    ]);
    $response->assertRedirect('/mytournaments');
    $this->assertAuthenticated();

    auth()->logout();
    $this->post('/login', [
        'email' => 'viewer.account@example.com',
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('password');
});

test('login rejects unknown accounts and incorrect passwords with specific errors', function () {
    $this->seed();

    $this->post('/login', [
        'email' => 'missing.user@example.com',
        'password' => 'StrongPass123!',
    ])->assertSessionHasErrors('email');

    $this->post('/login', [
        'email' => 'player@leaguehub.com',
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('password');
});

test('selected manager role cannot bypass credentials or match another role', function () {
    $this->seed();

    $this->get('/demo-login/manager')->assertNotFound();

    $this->post('/login', [
        'email' => 'viewer@leaguehub.com',
        'password' => 'wrong-password',
        'role' => 'viewer',
    ])->assertSessionHasErrors('password');

    $this->post('/login', [
        'email' => 'viewer@leaguehub.com',
        'password' => 'password',
        'role' => 'manager',
    ])->assertSessionHasErrors('role');
});

test('registration rejects duplicate normalized email addresses', function () {
    $this->seed();

    $this->post('/register', [
        'name' => 'Duplicate Viewer',
        'email' => 'VIEWER@leaguehub.com',
        'password' => 'StrongPass123!',
        'password_confirmation' => 'StrongPass123!',
        'role' => 'viewer',
        'areal_zone' => 'Metro Central',
    ])->assertSessionHasErrors('email');
});
