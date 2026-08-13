<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $this->get('/register')->assertOk();
});

test('new users can register and are assigned the viewer role', function () {
    seedRolesAndPermissions();

    $response = $this->post('/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');

    $user = User::where('email', 'jane@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('viewer'))->toBeTrue()
        ->and($user->hasRole('admin'))->toBeFalse();

    $this->assertAuthenticatedAs($user);
});

test('registration requires matching password confirmation', function () {
    seedRolesAndPermissions();

    $this->post('/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'not-the-same',
    ])->assertSessionHasErrors('password');

    expect(User::where('email', 'jane@example.com')->exists())->toBeFalse();
});

test('registration requires a unique email', function () {
    seedRolesAndPermissions();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post('/register', [
        'name' => 'Jane Doe',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');
});

test('login screen can be rendered', function () {
    $this->get('/login')->assertOk();
});

test('users can log in with correct credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('correct-password'),
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'correct-password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
});

test('users cannot log in with incorrect credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('correct-password'),
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors();

    $this->assertGuest();
});

test('authenticated users can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});

test('guests cannot access the dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can access the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});