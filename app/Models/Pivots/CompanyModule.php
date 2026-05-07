<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CompanyModule extends Pivot
{
    use HasUlids, LogsActivity, SoftDeletes;

    protected $table = 'company_module';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'company_id',
        'module_id',
        'is_enabled',
        'enabled_at',
        'disabled_at',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled'  => 'boolean',
            'enabled_at'  => 'datetime',
            'disabled_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['company_id', 'module_id', 'is_enabled', 'enabled_at', 'disabled_at'])
            ->logOnlyDirty();
    }
}
