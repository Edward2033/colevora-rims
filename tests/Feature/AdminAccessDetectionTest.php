<?php

use App\Models\User;

it('treats users with admin type as admins even without explicit admin roles', function () {
    $user = User::factory()->create([
        'user_type' => 'admin',
    ]);

    expect($user->isAdmin())->toBeTrue();
    expect($user->isCustomer())->toBeFalse();
});

it('treats users with customer type as customers even without customer roles', function () {
    $user = User::factory()->create([
        'user_type' => 'customer',
    ]);

    expect($user->isCustomer())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
});
