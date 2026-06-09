<?php

use App\Support\AdminAccess;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    config(['admin.password_hash' => Hash::make('secret-password')]);
});

test('admin access requires a configured password hash', function () {
    $adminAccess = app(AdminAccess::class);

    expect($adminAccess->isConfigured())->toBeTrue();

    config(['admin.password_hash' => null]);

    expect($adminAccess->isConfigured())->toBeFalse();
});

test('admin access authenticates with the configured password hash', function () {
    $adminAccess = app(AdminAccess::class);
    $request = request();

    expect($adminAccess->authenticate($request, 'secret-password'))->toBeTrue()
        ->and($adminAccess->check($request))->toBeTrue()
        ->and(session()->get($adminAccess->sessionKey()))->toBeTrue()
        ->and(session()->get($adminAccess->confirmedAtKey()))->toBeInt();
});

test('admin access rejects an invalid password', function () {
    $adminAccess = app(AdminAccess::class);
    $request = request();

    expect($adminAccess->authenticate($request, 'wrong-password'))->toBeFalse()
        ->and($adminAccess->check($request))->toBeFalse();
});
