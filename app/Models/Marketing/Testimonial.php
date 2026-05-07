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
    'company',
    'quote',
    'photo',
    'is_featured',
    'display_order',
    'is_published',
])]
class Testimonial extends Model
{
    use HasUlids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'role', 'company', 'is_featured', 'is_published', 'display_order'])
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'is_featured'  => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
