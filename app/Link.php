<?php

namespace App;

use App\Enums\LinkVisibility;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use RuntimeException;

#[Fillable([
    'slug',
    'platform_id',
    'destination_url',
    'title',
    'description',
    'logo_url',
    'is_active',
    'is_listed',
    'visibility',
    'sort_order',
    'expires_at',
])]
class Link extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $attributes = [
        'is_active' => true,
        'is_listed' => true,
        'visibility' => LinkVisibility::Featured->value,
        'sort_order' => 0,
        'clicks_count' => 0,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function destinationHost(): string
    {
        $host = parse_url($this->destination_url, PHP_URL_HOST);

        return is_string($host) && $host !== '' ? $host : $this->destination_url;
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function resolvedLogoUrl(): ?string
    {
        return $this->logo_url ?: $this->platform?->logo_url;
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query
            ->reachable()
            ->where('visibility', LinkVisibility::Featured->value);
    }

    public function scopeReachable(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isFeatured(): bool
    {
        return $this->visibility === LinkVisibility::Featured;
    }

    public function isHidden(): bool
    {
        return $this->visibility === LinkVisibility::Hidden;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isReachable(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    public static function generateUniqueSlug(int $length = 6): string
    {
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $slug = Str::lower(Str::random($length));

            if (static::withTrashed()->where('slug', $slug)->doesntExist()) {
                return $slug;
            }
        }

        throw new RuntimeException('Unable to generate a unique link slug.');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_listed' => 'boolean',
            'visibility' => LinkVisibility::class,
            'sort_order' => 'integer',
            'clicks_count' => 'integer',
            'last_clicked_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
