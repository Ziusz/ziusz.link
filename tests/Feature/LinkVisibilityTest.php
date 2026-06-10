<?php

namespace Tests\Feature;

use App\Enums\LinkVisibility;
use App\Link;

test('hidden links receive short temporary slugs by default', function () {
    $link = Link::factory()->hidden()->create();

    expect($link->slug)
        ->toHaveLength(6)
        ->and($link->visibility)
        ->toBe(LinkVisibility::Hidden)
        ->and($link->is_listed)
        ->toBeFalse()
        ->and($link->expires_at)
        ->not->toBeNull()
        ->and($link->expires_at->isBetween(now()->addDays(13), now()->addDays(15)))
        ->toBeTrue();
});

test('featured query excludes hidden expired and inactive links', function () {
    $featured = Link::factory()->create(['title' => 'Featured']);

    Link::factory()->hidden()->create(['title' => 'Hidden']);
    Link::factory()->create(['title' => 'Expired', 'expires_at' => now()->subMinute()]);
    Link::factory()->create(['title' => 'Inactive', 'is_active' => false]);

    expect(Link::query()->featured()->pluck('id')->all())->toBe([$featured->id]);
});

test('generated short slugs are unique', function () {
    $first = Link::factory()->hidden()->create();
    $second = Link::factory()->hidden()->create();

    expect($first->slug)->not->toBe($second->slug);
});
