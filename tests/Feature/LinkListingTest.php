<?php

namespace Tests\Feature;

use App\Link;
use Database\Seeders\LinkSeeder;

test('home lists active and listed links in sort order', function () {
    $second = Link::factory()->create([
        'slug' => 'second',
        'title' => 'Second Link',
        'description' => 'Second description',
        'destination_url' => 'https://example.com/second',
        'clicks_count' => 99,
        'sort_order' => 20,
    ]);

    $first = Link::factory()->create([
        'slug' => 'first',
        'title' => 'First Link',
        'description' => 'First description',
        'destination_url' => 'https://example.com/first',
        'sort_order' => 10,
    ]);

    Link::factory()->create([
        'title' => 'Hidden Link',
        'is_listed' => false,
    ]);

    Link::factory()->create([
        'title' => 'Inactive Link',
        'is_active' => false,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeTextInOrder([
            'First Link',
            'Second Link',
        ])
        ->assertSee(route('links.redirect', $first), false)
        ->assertSee(route('links.redirect', $second), false)
        ->assertDontSeeText('99 clicks')
        ->assertDontSee(route('admin.dashboard'), false)
        ->assertDontSeeText('Admin')
        ->assertDontSeeText('Hidden Link')
        ->assertDontSeeText('Inactive Link');
});

test('home shows an empty state when no links are published', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('No links here yet.');
});

test('link seeder creates sample links with varied states', function () {
    $this->seed(LinkSeeder::class);

    $link = new Link;

    expect($link->newQuery()->count())
        ->toBe(15)
        ->and($link->newQuery()->where('is_active', false)->exists())
        ->toBeTrue()
        ->and($link->newQuery()->where('is_listed', false)->exists())
        ->toBeTrue()
        ->and($link->newQuery()->where('clicks_count', '>', 0)->exists())
        ->toBeTrue()
        ->and($link->newQuery()->whereNull('description')->exists())
        ->toBeTrue()
        ->and($link->newQuery()->whereNotNull('last_clicked_at')->exists())
        ->toBeTrue();
});
