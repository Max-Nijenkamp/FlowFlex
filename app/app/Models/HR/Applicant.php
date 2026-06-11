<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\States\HR\Applicant\ApplicantState;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Applicant extends Model
{
    use BelongsToCompany, HasStates, HasUlids, SoftDeletes;

    protected $table = 'hr_applicants';

    protected $fillable = ['company_id', 'requisition_id', 'first_name', 'last_name', 'email', 'phone', 'cv_path', 'status', 'source', 'rejection_reason'];

    protected function casts(): array
    {
        return ['status' => ApplicantState::class];
    }

    /** @return BelongsTo<JobRequisition, $this> */
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(JobRequisition::class, 'requisition_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
