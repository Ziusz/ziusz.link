<?php

namespace Tests\Feature;

use App\Enums\LinkVisibility;
use App\Link;
use App\Platform;
use Database\Seeders\LinkSeeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

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
        'visibility' => LinkVisibility::Hidden,
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

test('home renders platform logos and prefers link overrides', function () {
    $github = Platform::factory()->create([
        'name' => 'GitHub',
        'logo_url' => 'https://cdn.example.com/github.svg',
    ]);
    $youtube = Platform::factory()->create([
        'name' => 'YouTube',
        'logo_url' => 'https://cdn.example.com/youtube.svg',
    ]);

    Link::factory()->for($github)->create([
        'title' => 'GitHub',
        'sort_order' => 10,
    ]);
    Link::factory()->for($youtube)->create([
        'title' => 'YouTube',
        'logo_url' => 'https://cdn.example.com/custom-youtube.svg',
        'sort_order' => 20,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('src="https://cdn.example.com/github.svg"', false)
        ->assertSee('alt="GitHub"', false)
        ->assertSee('src="https://cdn.example.com/custom-youtube.svg"', false)
        ->assertSee('alt="YouTube"', false)
        ->assertDontSee('src="https://cdn.example.com/youtube.svg"', false);
});

test('link seeder creates sample links with varied states', function () {
    $this->app->usePublicPath(storage_path('framework/testing/public'));

    Http::fake([
        'cdn.simpleicons.org/*' => Http::response('<svg viewBox="0 0 1 1"></svg>', 200),
    ]);

    try {
        $this->seed(LinkSeeder::class);

        $link = new Link;

        expect($link->newQuery()->count())
            ->toBe(15)
            ->and(Platform::query()->exists())
            ->toBeTrue()
            ->and($link->newQuery()->whereNotNull('platform_id')->count())
            ->toBe(15)
            ->and($link->newQuery()->where('is_active', false)->exists())
            ->toBeTrue()
            ->and($link->newQuery()->where('visibility', LinkVisibility::Hidden->value)->exists())
            ->toBeTrue()
            ->and($link->newQuery()->where('clicks_count', '>', 0)->exists())
            ->toBeTrue()
            ->and($link->newQuery()->whereNull('description')->exists())
            ->toBeTrue()
            ->and($link->newQuery()->whereNotNull('last_clicked_at')->exists())
            ->toBeTrue();
    } finally {
        File::deleteDirectory(public_path('platform-logos'));

        $this->app->usePublicPath(base_path('public'));
    }
});
