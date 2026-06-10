<?php

namespace Tests\Feature;

use App\Link;

test('active links redirect to their destination and record the click', function () {
    $link = Link::factory()->create([
        'destination_url' => 'https://laravel.com/docs',
    ]);

    $this->get(route('links.redirect', $link))
        ->assertRedirect('https://laravel.com/docs');

    $link->refresh();

    expect($link->clicks_count)
        ->toBe(1)
        ->and($link->last_clicked_at)
        ->not->toBeNull();
});

test('hidden links can still redirect', function () {
    $link = Link::factory()->hidden()->create([
        'destination_url' => 'https://example.com/private',
    ]);

    $this->get(route('links.redirect', $link))
        ->assertRedirect('https://example.com/private');
});

test('inactive links are not reachable', function () {
    $link = Link::factory()->create([
        'is_active' => false,
    ]);

    $this->get(route('links.redirect', $link))
        ->assertNotFound();

    expect($link->refresh()->clicks_count)->toBe(0);
});

test('expired links are not reachable', function () {
    $link = Link::factory()->hidden(now()->subMinute())->create();

    $this->get(route('links.redirect', $link))
        ->assertNotFound();

    expect($link->refresh()->clicks_count)->toBe(0);
});
