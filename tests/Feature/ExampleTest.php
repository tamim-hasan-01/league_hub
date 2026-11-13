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
