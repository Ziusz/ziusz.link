<?php

use App\Support\AdminAccess;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    config(['admin.password_hash' => Hash::make('secret-password')]);
});

test('admin login page can be rendered', function () {
    $response = $this->get(route('admin.login'));

    $response
        ->assertOk()
        ->assertSeeText('Admin login');
});

test('admin login page reports missing password hash', function () {
    config(['admin.password_hash' => null]);

    $response = $this->get(route('admin.login'));

    $response
        ->assertOk()
        ->assertSeeText('Admin password is not configured.');
});

test('authenticated admins are redirected away from the login page', function () {
    $adminAccess = app(AdminAccess::class);

    $response = $this->withSession([
        $adminAccess->sessionKey() => true,
    ])->get(route('admin.login'));

    $response->assertRedirect(route('admin.dashboard'));
});

test('admin can unlock the panel', function () {
    Livewire::test('pages::admin.login')
        ->set('password', 'secret-password')
        ->call('unlock')
        ->assertRedirect(route('admin.dashboard'));

    $adminAccess = app(AdminAccess::class);

    expect(session()->get($adminAccess->sessionKey()))->toBeTrue()
        ->and(session()->get($adminAccess->confirmedAtKey()))->toBeInt();
});

test('admin cannot unlock with an invalid password', function () {
    Livewire::test('pages::admin.login')
        ->set('password', 'wrong-password')
        ->call('unlock')
        ->assertHasErrors('password');

    $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
});
