<?php

use App\Enums\LinkLifetime;
use App\Enums\LinkVisibility;
use App\Link;
use App\Platform;
use App\Support\AdminAccess;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config(['admin.password_hash' => Hash::make('secret-password')]);
});

test('guest admins are redirected away from the create form', function () {
    $this->get(route('admin.links.create'))
        ->assertRedirect(route('admin.login'));
});

test('authenticated admins can render the create form', function () {
    $adminAccess = app(AdminAccess::class);
    Platform::factory()->create(['name' => 'GitHub']);

    $this->withSession([$adminAccess->sessionKey() => true])
        ->get(route('admin.links.create'))
        ->assertOk()
        ->assertSeeText('New link')
        ->assertSeeText('GitHub')
        ->assertSeeText('14 days')
        ->assertSeeText('Permanent');
});

test('authenticated admins can create a hidden temporary link with generated slug', function () {
    $adminAccess = app(AdminAccess::class);
    $platform = Platform::factory()->create(['name' => 'GitHub']);

    $response = $this->withSession([$adminAccess->sessionKey() => true])
        ->post(route('admin.links.store'), [
            'description' => 'Private profile link.',
            'destination_url' => 'https://github.com/ziusz',
            'is_active' => '1',
            'lifetime' => LinkLifetime::FourteenDays->value,
            'platform_id' => (string) $platform->id,
            'slug' => '',
            'sort_order' => '10',
            'title' => 'GitHub',
            'visibility' => LinkVisibility::Hidden->value,
        ]);

    $link = Link::query()->firstOrFail();

    $response->assertRedirect(route('admin.links.show', $link));

    expect($link->slug)
        ->toHaveLength(6)
        ->and($link->visibility)
        ->toBe(LinkVisibility::Hidden)
        ->and($link->is_listed)
        ->toBeFalse()
        ->and($link->platform?->is($platform))
        ->toBeTrue()
        ->and($link->expires_at?->isBetween(now()->addDays(13), now()->addDays(15)))
        ->toBeTrue();
});

test('authenticated admins can create a permanent custom alias', function () {
    $adminAccess = app(AdminAccess::class);

    $response = $this->withSession([$adminAccess->sessionKey() => true])
        ->post(route('admin.links.store'), [
            'destination_url' => 'https://example.com/private',
            'is_active' => '1',
            'lifetime' => LinkLifetime::Permanent->value,
            'slug' => 'private-alias',
            'title' => 'Private Alias',
            'visibility' => LinkVisibility::Hidden->value,
        ]);

    $link = Link::query()->firstOrFail();

    $response->assertRedirect(route('admin.links.show', $link));

    expect($link->slug)
        ->toBe('private-alias')
        ->and($link->expires_at)
        ->toBeNull();
});

test('link aliases must be unique', function () {
    $adminAccess = app(AdminAccess::class);
    Link::factory()->create(['slug' => 'taken']);

    $this->withSession([$adminAccess->sessionKey() => true])
        ->from(route('admin.links.create'))
        ->post(route('admin.links.store'), [
            'destination_url' => 'https://example.com',
            'is_active' => '1',
            'slug' => 'taken',
            'title' => 'Duplicate',
            'visibility' => LinkVisibility::Featured->value,
        ])
        ->assertRedirect(route('admin.links.create'))
        ->assertSessionHasErrors('slug');
});

test('authenticated admins can update links', function () {
    $adminAccess = app(AdminAccess::class);
    $platform = Platform::factory()->create(['name' => 'YouTube']);
    $link = Link::factory()->create([
        'slug' => 'old-alias',
        'title' => 'Old title',
    ]);
    Storage::fake('public');
    Http::fake([
        'cdn.example.com/*' => Http::response('<svg viewBox="0 0 1 1"></svg>', 200, ['content-type' => 'image/svg+xml']),
    ]);

    $this->withSession([$adminAccess->sessionKey() => true])
        ->put(route('admin.links.update', $link), [
            'description' => 'Updated description.',
            'destination_url' => 'https://youtube.com/@ziusz',
            'is_active' => '1',
            'lifetime' => LinkLifetime::Permanent->value,
            'logo_url' => 'https://cdn.example.com/custom.svg',
            'platform_id' => (string) $platform->id,
            'slug' => 'youtube',
            'sort_order' => '25',
            'title' => 'YouTube',
            'visibility' => LinkVisibility::Featured->value,
        ])
        ->assertRedirect(route('admin.links.show', $link->refresh()));

    $link->refresh();

    expect($link->slug)
        ->toBe('youtube')
        ->and($link->title)
        ->toBe('YouTube')
        ->and($link->destination_url)
        ->toBe('https://youtube.com/@ziusz')
        ->and($link->description)
        ->toBe('Updated description.')
        ->and($link->platform?->is($platform))
        ->toBeTrue()
        ->and($link->visibility)
        ->toBe(LinkVisibility::Featured)
        ->and($link->is_listed)
        ->toBeTrue()
        ->and($link->expires_at)
        ->toBeNull()
        ->and($link->logo_url)
        ->toBe('link-logos/youtube.svg')
        ->and($link->sort_order)
        ->toBe(25);

    Storage::disk('public')->assertExists('link-logos/youtube.svg');
});
