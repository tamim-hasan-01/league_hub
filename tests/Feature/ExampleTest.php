<?php

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
