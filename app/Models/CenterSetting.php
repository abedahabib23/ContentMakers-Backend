<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'city',
    'phone',
    'email',
    'logo_path',
    'accepts_registrations',
    'auto_issue_certificate',
    'notify_email_on_new_request',
])]
class CenterSetting extends Model
{
    /**
     * Mirrors the columns' DB-level defaults — Eloquent doesn't re-fetch
     * server-side defaults after an insert, so without this a freshly
     * created instance's boolean toggles would read as null in the same
     * request even though the row itself has real true/false values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'accepts_registrations' => true,
        'auto_issue_certificate' => true,
        'notify_email_on_new_request' => false,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accepts_registrations' => 'boolean',
            'auto_issue_certificate' => 'boolean',
            'notify_email_on_new_request' => 'boolean',
        ];
    }

    /**
     * The single settings row, created with its column defaults the first
     * time anything asks for it.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
