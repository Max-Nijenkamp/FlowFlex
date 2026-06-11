<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SequenceEnrolment extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_sequence_enrolments';

    protected $fillable = ['company_id', 'sequence_id', 'contact_id', 'deal_id', 'current_step', 'status', 'next_step_at', 'variant_map', 'enrolled_at'];

    protected function casts(): array
    {
        return ['current_step' => 'integer', 'next_step_at' => 'datetime', 'variant_map' => 'array', 'enrolled_at' => 'datetime'];
    }

    /** @return BelongsTo<Sequence, $this> */
    public function sequence(): BelongsTo
    {
        return $this->belongsTo(Sequence::class, 'sequence_id');
    }
}
