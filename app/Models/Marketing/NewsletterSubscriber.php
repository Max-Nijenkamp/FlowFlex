<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'email',
    'status',
    'source',
    'subscribed_at',
    'unsubscribed_at',
    'double_opt_in_confirmed',
    'double_opt_in_sent_at',
])]
class NewsletterSubscriber extends Model
{
    use HasUlids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['email', 'status', 'source', 'subscribed_at', 'unsubscribed_at'])
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'subscribed_at'          => 'datetime',
            'unsubscribed_at'        => 'datetime',
            'double_opt_in_sent_at'  => 'datetime',
            'double_opt_in_confirmed' => 'boolean',
        ];
    }
}
