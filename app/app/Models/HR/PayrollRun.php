<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRun extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'payroll_runs';

    protected $fillable = [
        'company_id',
        'name',
        'period_start',
        'period_end',
        'pay_date',
        'status',
        'total_gross',
        'total_net',
        'total_deductions',
        'currency',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'period_start'     => 'date',
        'period_end'       => 'date',
        'pay_date'         => 'date',
        'total_gross'      => 'decimal:2',
        'total_net'        => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'approved_at'      => 'datetime',
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class, 'run_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
