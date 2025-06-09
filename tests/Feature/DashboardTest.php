<?php

use App\Models\User;
use Spatie\Permission\Models\Role;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    Role::firstOrCreate(['name' => 'user']);

    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $user->assignRole('user'); 

    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});