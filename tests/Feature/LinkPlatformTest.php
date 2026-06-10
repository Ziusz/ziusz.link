<?php

namespace Tests\Feature;

use App\Link;
use App\Platform;
use Illuminate\Support\Facades\Schema;

test('platform tables and link logo columns exist', function () {
    expect(Schema::hasColumns('platforms', [
        'id',
        'slug',
        'name',
        'domain',
        'logo_url',
        'created_at',
        'updated_at',
    ]))->toBeTrue()
        ->and(Schema::hasColumns('links', [
            'platform_id',
            'logo_url',
        ]))->toBeTrue();
});

test('links may belong to a platform', function () {
    $platform = Platform::factory()->create();
    $link = Link::factory()->create([
        'platform_id' => $platform->id,
    ]);

    expect($link->refresh()->platform->is($platform))->toBeTrue()
        ->and($platform->links()->first()?->is($link))->toBeTrue();
});

test('links resolve custom logos before platform logos', function () {
    $platform = Platform::factory()->create([
        'logo_url' => 'https://cdn.example.com/platform.svg',
    ]);

    $platformLink = Link::factory()->create([
        'platform_id' => $platform->id,
        'logo_url' => null,
    ]);

    $customLink = Link::factory()->create([
        'platform_id' => $platform->id,
        'logo_url' => 'https://cdn.example.com/custom.svg',
    ]);

    expect($platformLink->refresh()->resolvedLogoUrl())
        ->toBe('https://cdn.example.com/platform.svg')
        ->and($customLink->refresh()->resolvedLogoUrl())
        ->toBe('https://cdn.example.com/custom.svg');
});
