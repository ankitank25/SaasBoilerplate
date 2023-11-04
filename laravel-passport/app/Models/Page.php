<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, HasUuids;

    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    protected $fillable = [
        'id',
        'title',
        'url_key',
        'show_title',
        'content',
        'layout',
        'meta_title',
        'meta_description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'show_title' => 'boolean',
        'status' => 'boolean',
    ];
}
