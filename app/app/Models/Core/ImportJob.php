<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportJob extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'created_by',
        'entity_type',
        'status',
        'duplicate_strategy',
        'total_rows',
        'imported_rows',
        'skipped_rows',
        'failed_rows',
        'column_mapping',
        'file_path',
        'error_log_path',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'column_mapping' => 'array',
        'started_at'     => 'datetime',
        'finished_at'    => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(ImportJobRow::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }
}
