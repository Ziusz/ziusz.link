<?php

namespace App;

use App\Support\LogoStore;
use Database\Factories\PlatformFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'name',
    'domain',
    'logo_url',
])]
class Platform extends Model
{
    /** @use HasFactory<PlatformFactory> */
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function logoUrl(): ?string
    {
        return LogoStore::isStoredPath($this->logo_url)
            ? route('logos.platforms.show', $this)
            : null;
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }
}
