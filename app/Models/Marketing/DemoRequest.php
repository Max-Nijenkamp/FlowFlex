<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'first_name',
    'last_name',
    'email',
    'company_name',
    'company_size',
    'modules_interested',
    'heard_from',
    'notes',
    'phone',
    'ip_address',
    'user_agent',
    'utm_source',
    'utm_medium',
    'utm_campaign',
    'utm_content',
    'utm_term',
    'status',
    'assigned_to',
    'scheduled_at',
    'notes_internal',
])]
class DemoRequest extends Model
{
    use HasUlids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'company_name', 'status', 'assigned_to', 'scheduled_at'])
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'modules_interested' => 'array',
            'scheduled_at'       => 'datetime',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
