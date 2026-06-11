<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sequence extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'crm_sequences';

    protected $fillable = ['company_id', 'name', 'owner_id', 'trigger_type', 'trigger_config', 'is_active'];

    protected function casts(): array
    {
        return ['trigger_config' => 'array', 'is_active' => 'boolean'];
    }

    /** @return HasMany<SequenceStep, $this> */
    public function steps(): HasMany
    {
        return $this->hasMany(SequenceStep::class, 'sequence_id');
    }

    /** @return HasMany<SequenceEnrolment, $this> */
    public function enrolments(): HasMany
    {
        return $this->hasMany(SequenceEnrolment::class, 'sequence_id');
    }
}
