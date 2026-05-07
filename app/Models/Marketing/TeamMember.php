<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'name',
    'role',
    'bio',
    'photo',
    'linkedin_url',
    'twitter_url',
    'display_order',
    'is_published',
])]
class TeamMember extends Model
{
    use HasUlids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'role', 'display_order', 'is_published'])
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
