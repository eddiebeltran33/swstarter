<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia;
// Removed: use Tests\TestCase;
// Note: TestCase is typically handled by tests/Pest.php

test('home redirects to dashboard', function () {
    $response = $this->get('/');
    $response->assertStatus(302);
    $response->assertRedirect(route('dashboard'));
});

test('dashboard renders successfully', function () {
    // If dashboard requires authentication, this test might need adjustment
    // by creating a user and using $this->actingAs($user).
    // For now, assuming it's accessible.
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Dashboard')
    );
});
