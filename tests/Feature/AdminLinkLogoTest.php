<?php

use App\Platform;
use Database\Seeders\PlatformSeeder;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->app->usePublicPath(storage_path('framework/testing/public'));

    File::deleteDirectory(public_path('platform-logos'));
});

afterEach(function (): void {
    File::deleteDirectory(public_path('platform-logos'));

    $this->app->usePublicPath(base_path('public'));
});

test('platform seeder stores remote logos locally', function () {
    Http::preventStrayRequests();

    Http::fake([
        'cdn.simpleicons.org/*' => Http::response('<svg viewBox="0 0 1 1"></svg>', 200),
    ]);

    $this->seed(PlatformSeeder::class);

    $github = Platform::query()->where('slug', 'github')->firstOrFail();
    $website = Platform::query()->where('slug', 'website')->firstOrFail();

    expect($github->logo_url)
        ->toBe('/platform-logos/github.svg')
        ->and(File::exists(public_path('platform-logos/github.svg')))
        ->toBeTrue()
        ->and(File::get(public_path('platform-logos/github.svg')))
        ->toContain('<svg')
        ->and($website->logo_url)
        ->toBeNull();

    Http::assertSentCount(19);
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://cdn.simpleicons.org/github');
});
