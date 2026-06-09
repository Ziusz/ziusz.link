<?php

namespace App;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'slug',
    'destination_url',
    'title',
    'description',
    'is_active',
    'is_listed',
    'sort_order',
])]
class Link extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $attributes = [
        'is_active' => true,
        'is_listed' => true,
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
            'sort_order' => 'integer',
            'clicks_count' => 'integer',
            'last_clicked_at' => 'datetime',
        ];
    }
}
