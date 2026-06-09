<?php

use App\Link;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

test('links table has the link shortener and listing columns', function () {
    expect(Schema::hasColumns('links', [
        'id',
        'slug',
        'destination_url',
        'title',
        'description',
        'is_active',
        'is_listed',
        'sort_order',
        'clicks_count',
        'last_clicked_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ]))->toBeTrue();
});

test('links can be persisted with sensible defaults', function () {
    $link = Link::create([
        'slug' => 'github',
        'destination_url' => 'https://github.com/ziusz',
        'title' => 'GitHub',
    ])->refresh();

    expect($link->is_active)->toBeTrue()
        ->and($link->is_listed)->toBeTrue()
        ->and($link->sort_order)->toBe(0)
        ->and($link->clicks_count)->toBe(0)
        ->and($link->last_clicked_at)->toBeNull();
});

test('link slugs must be unique', function () {
    Link::create([
        'slug' => 'github',
        'destination_url' => 'https://github.com/ziusz',
    ]);

    expect(fn () => Link::create([
        'slug' => 'github',
        'destination_url' => 'https://github.com/laravel',
    ]))->toThrow(QueryException::class);
});

test('links are soft deleted', function () {
    $link = Link::create([
        'slug' => 'github',
        'destination_url' => 'https://github.com/ziusz',
    ]);

    $link->delete();

    expect(Link::find($link->id))->toBeNull()
        ->and(Link::withTrashed()->find($link->id)?->trashed())->toBeTrue();
});
