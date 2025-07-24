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

    public static function validateUniqueName($email, $ignoreId = null)
    {
        $normalizedValue = preg_replace('/\s+/', '', $email);
        $query = static::whereRaw('REPLACE(email, " ", "") = ?', [$normalizedValue]);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return !$query->exists();
    }

    // Validation Rules
    public static function validationRules($record = null): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:100',
                function ($attribute, $value, $fail) use ($record) {
                    if (!self::validateUniqueName($value, $record?->id)) {
                        $fail('This email address already exists.');
                    }
                }
            ]
        ];
    }

    public static function getValidationMesages(): array
    {
        return [
            'email' => [
                'required' => 'The email address field is required.',
                'email' => 'The email address must be a valid email format and the first letter must not contain spaces.',
                'max' => 'The email address may not be greater than 100 characters.'
            ]
        ];
    }
}
