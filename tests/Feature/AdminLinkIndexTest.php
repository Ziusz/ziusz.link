<?php

use App\Enums\LinkVisibility;
use App\Link;
use App\Platform;
use App\Support\AdminAccess;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    config(['admin.password_hash' => Hash::make('secret-password')]);
});

test('guest admins are redirected away from the links index', function () {
    $this->get(route('admin.links.index'))
        ->assertRedirect(route('admin.login'));
});

test('authenticated admins can list non deleted links', function () {
    $adminAccess = app(AdminAccess::class);
    $platform = Platform::factory()->create([
        'name' => 'GitHub',
        'logo_url' => 'platform-logos/github.svg',
    ]);

    $first = Link::factory()->for($platform)->create([
        'title' => 'Alpha',
        'slug' => 'alpha',
        'clicks_count' => 5,
    ]);

    $second = Link::factory()->create([
        'title' => 'Beta',
        'slug' => 'beta',
        'clicks_count' => 20,
        'visibility' => LinkVisibility::Hidden,
        'is_listed' => false,
    ]);

    $deleted = Link::factory()->create(['title' => 'Deleted Link']);
    $deleted->delete();

    $this->withSession([$adminAccess->sessionKey() => true])
        ->get(route('admin.links.index'))
        ->assertOk()
        ->assertSeeText('Links')
        ->assertSeeText('Alpha')
        ->assertSeeText('Beta')
        ->assertSeeText('GitHub')
        ->assertSee('src="'.route('logos.platforms.show', $platform).'"', false)
        ->assertSee(route('admin.links.show', $first), false)
        ->assertSee(route('admin.links.destroy', $second), false)
        ->assertDontSeeText('Deleted Link');
});

test('authenticated admins can sort links by clicks', function () {
    $adminAccess = app(AdminAccess::class);

    Link::factory()->create(['title' => 'Quiet Link', 'clicks_count' => 1]);
    Link::factory()->create(['title' => 'Popular Link', 'clicks_count' => 99]);

    $this->withSession([$adminAccess->sessionKey() => true])
        ->get(route('admin.links.index', [
            'sort' => 'clicks_count',
            'direction' => 'desc',
        ]))
        ->assertOk()
        ->assertSeeTextInOrder([
            'Popular Link',
            'Quiet Link',
        ]);
});

test('authenticated admins can view link details', function () {
    $adminAccess = app(AdminAccess::class);
    $platform = Platform::factory()->create([
        'name' => 'GitHub',
        'logo_url' => 'platform-logos/github.svg',
    ]);

    $link = Link::factory()->for($platform)->create([
        'title' => 'Profile',
        'slug' => 'profile',
        'destination_url' => 'https://github.com/ziusz',
        'description' => 'Code and projects.',
        'clicks_count' => 12,
    ]);

    $this->withSession([$adminAccess->sessionKey() => true])
        ->get(route('admin.links.show', $link))
        ->assertOk()
        ->assertSeeText('Profile')
        ->assertSeeText('https://github.com/ziusz')
        ->assertSeeText('GitHub')
        ->assertSeeText(route('logos.platforms.show', $platform))
        ->assertSeeText('Code and projects.')
        ->assertSeeText('12');
});

test('authenticated admins can soft delete links', function () {
    $adminAccess = app(AdminAccess::class);
    $link = Link::factory()->create([
        'title' => 'Disposable',
        'slug' => 'disposable',
    ]);

    $this->withSession([$adminAccess->sessionKey() => true])
        ->delete(route('admin.links.destroy', $link))
        ->assertRedirect(route('admin.links.index'));

    $this->assertSoftDeleted($link);

    $this->withSession([$adminAccess->sessionKey() => true])
        ->get(route('admin.links.index'))
        ->assertOk()
        ->assertDontSeeText('Disposable');

    $this->withSession([$adminAccess->sessionKey() => true])
        ->get(route('admin.links.show', $link))
        ->assertNotFound();

    $this->get(route('links.redirect', $link))->assertNotFound();
});
