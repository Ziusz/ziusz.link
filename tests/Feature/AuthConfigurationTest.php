<?php

use Illuminate\Support\Facades\Auth;

test('web guard resolves without an application user provider', function () {
    expect(Auth::id())->toBeNull();
});

test('home page can be rendered after the web guard is resolved', function () {
    Auth::id();

    $response = $this->get(route('home'));

    $response->assertOk();
});
