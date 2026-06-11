<?php

use App\Platform;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PlatformSeeder;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('platform seeder stores remote logos locally', function () {
    Http::preventStrayRequests();
    Storage::fake('public');

    Http::fake([
        'cdn.simpleicons.org/*' => Http::response('<svg viewBox="0 0 1 1"></svg>', 200, ['content-type' => 'image/svg+xml']),
        'upload.wikimedia.org/*' => Http::response('<svg viewBox="0 0 1 1"></svg>', 200, ['content-type' => 'image/svg+xml']),
    ]);

    $this->seed(PlatformSeeder::class);

    $github = Platform::query()->where('slug', 'github')->firstOrFail();
    $linkedin = Platform::query()->where('slug', 'linkedin')->firstOrFail();
    $website = Platform::query()->where('slug', 'website')->firstOrFail();

    expect($github->logo_url)
        ->toBe('platform-logos/github.svg')
        ->and($linkedin->logo_url)
        ->toBe('platform-logos/linkedin.svg')
        ->and($website->logo_url)
        ->toBeNull();

    Storage::disk('public')->assertExists('platform-logos/github.svg');
    Storage::disk('public')->assertExists('platform-logos/linkedin.svg');

    expect(Storage::disk('public')->get('platform-logos/github.svg'))
        ->toContain('<svg');

    $this->get(route('logos.platforms.show', $github))
        ->assertOk();

    Http::assertSentCount(19);
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://cdn.simpleicons.org/github');
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://upload.wikimedia.org/wikipedia/commons/8/81/LinkedIn_icon.svg');
});

test('database seeder does not refresh platform logos twice', function () {
    Http::preventStrayRequests();
    Storage::fake('public');

    Http::fake([
        'cdn.simpleicons.org/*' => Http::response('<svg viewBox="0 0 1 1"></svg>', 200, ['content-type' => 'image/svg+xml']),
        'upload.wikimedia.org/*' => Http::response('<svg viewBox="0 0 1 1"></svg>', 200, ['content-type' => 'image/svg+xml']),
    ]);

    $this->seed(DatabaseSeeder::class);

    expect(Platform::query()->count())
        ->toBe(20);

    Http::assertSentCount(19);
});
