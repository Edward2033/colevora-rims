<?php

use App\Models\User;

test('profile page is displayed', function () {
    $this->actingAs(User::factory()->create());
    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->patch('/settings/profile', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $user->refresh();
    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->patch('/settings/profile', [
        'name' => 'Test User',
        'email' => $user->email,
    ]);

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->delete('/settings/profile', [
        'password' => 'password',
    ]);

    $response->assertRedirect();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->delete('/settings/profile', [
        'password' => 'wrong-password',
    ]);

    expect($user->fresh())->not->toBeNull();
});
