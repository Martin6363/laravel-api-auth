<?php

use Martin6363\ApiAuth\Tests\Models\User;
use Illuminate\Support\Facades\Notification;

it('can send a password reset link', function () {
    Notification::fake();

    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(200);
});

it('fails to send reset link for non-existing user', function () {
    $response = $this->postJson('/api/auth/forgot-password', [
        'email' => 'notfound@example.com',
    ]);

    $response->assertStatus(422);
});