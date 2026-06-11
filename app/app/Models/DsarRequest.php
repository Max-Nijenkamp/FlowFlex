<?php

declare(strict_types=1);

namespace App\Models;

use App\States\DsarRequest\DsarRequestState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\DsarRequestFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;

/**
 * @property string $id
 * @property string $company_id
 * @property string $subject_email
 * @property string $request_type
 * @property DsarRequestState $status
 * @property Carbon $due_at
 * @property Carbon|null $completed_at
 * @property string|null $result_path
 * @property string|null $rejection_reason
 */
class DsarRequest extends Model
{
    /** @use HasFactory<DsarRequestFactory> */
    use BelongsToCompany, HasFactory, HasStates, HasUlids, SoftDeletes;

    protected $fillable = [
        'company_id',
        'subject_email',
        'request_type',
        'status',
        'due_at',
        'completed_at',
        'result_path',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => DsarRequestState::class,
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
