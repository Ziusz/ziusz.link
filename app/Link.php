<?php

namespace App;

use Illuminate\Database\Eloquent\Attributes\Fillable;
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
    use SoftDeletes;

    protected $attributes = [
        'is_active' => true,
        'is_listed' => true,
        'sort_order' => 0,
        'clicks_count' => 0,
    ];

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
