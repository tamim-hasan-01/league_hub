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
    $login->assertSee('Quick demo access');

    $register = $this->get('/register');
    $register->assertOk();
    $register->assertSee('Create an account');
    $register->assertSee('Select your role');
});
