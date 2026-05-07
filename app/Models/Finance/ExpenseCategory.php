<?php

namespace App\Models\Finance;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ExpenseCategory extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'monthly_limit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'monthly_limit' => 'decimal:2',
            'is_active'     => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'monthly_limit', 'is_active'])
            ->logOnlyDirty();
    }
}
