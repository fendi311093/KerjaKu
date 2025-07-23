<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EmailAddress extends Model
{
    protected $fillable = ['email', 'is_active'];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        parent::booted();

        static::created(function ($model) {
            // Clear cache when a new email address is created
            Cache::forget('options_email_addresses');
        });

        static::updated(function ($model) {
            // Clear cache when an email address is updated
            Cache::forget('options_email_addresses');
        });

        static::deleted(function ($model) {
            // Clear cache when an email address is deleted
            Cache::forget('options_email_addresses');
        });
    }

    public static function getOptionsEmail(): array
    {
        return Cache::rememberForever('options_email_addresses', function () {
            return self::where('is_active', true)
                ->pluck('email', 'id')
                ->toArray();
        });
    }
}
