<?php

namespace App\Models\Finance;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RecurringInvoice extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'contact_id',
        'frequency',
        'next_run_at',
        'last_run_at',
        'is_active',
        'template_data',
    ];

    protected function casts(): array
    {
        return [
            'next_run_at'   => 'date',
            'last_run_at'   => 'date',
            'is_active'     => 'boolean',
            'template_data' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['frequency', 'next_run_at', 'is_active'])
            ->logOnlyDirty();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
