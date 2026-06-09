<?php

use App\Support\AdminAccess;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    config(['admin.password_hash' => Hash::make('secret-password')]);
});

test('guest admins are redirected to the admin login page', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.login'));
});

test('authenticated admins can view the admin dashboard', function () {
    $adminAccess = app(AdminAccess::class);

    $response = $this->withSession([
        $adminAccess->sessionKey() => true,
    ])->get(route('admin.dashboard'));

    $response
        ->assertOk()
        ->assertSeeText('Link manager');
});

test('authenticated admins can log out', function () {
    $adminAccess = app(AdminAccess::class);

    $response = $this->withSession([
        $adminAccess->sessionKey() => true,
    ])->post(route('admin.logout'));

    $response
        ->assertRedirect(route('admin.login'))
        ->assertSessionMissing($adminAccess->sessionKey());
});
