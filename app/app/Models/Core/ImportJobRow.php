<?php

declare(strict_types=1);

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportJobRow extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'import_job_id',
        'row_number',
        'status',
        'raw_data',
        'mapped_data',
        'errors',
    ];

    protected $casts = [
        'raw_data'    => 'array',
        'mapped_data' => 'array',
        'errors'      => 'array',
    ];

    public function importJob(): BelongsTo
    {
        return $this->belongsTo(ImportJob::class);
    }
}
