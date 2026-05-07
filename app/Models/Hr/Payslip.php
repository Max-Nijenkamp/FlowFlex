<?php

namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use App\Models\File;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Payslip extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'pay_run_id',
        'employee_id',
        'pay_run_employee_id',
        'pdf_file_id',
        'period_start',
        'period_end',
        'status',
        'pdf_path',
        'generated_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end'   => 'date',
            'generated_at' => 'datetime',
            'sent_at'      => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['generated_at', 'sent_at'])
            ->logOnlyDirty();
    }

    public function payRun(): BelongsTo
    {
        return $this->belongsTo(PayRun::class, 'pay_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function payRunEmployee(): BelongsTo
    {
        return $this->belongsTo(PayRunEmployee::class, 'pay_run_employee_id');
    }

    public function pdfFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'pdf_file_id');
    }
}
