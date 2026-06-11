<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Segment extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'crm_segments';

    protected $fillable = ['company_id', 'name', 'type', 'conditions', 'member_count'];

    protected function casts(): array
    {
        return ['conditions' => 'array', 'member_count' => 'integer'];
    }

    /** @return HasMany<SegmentMember, $this> */
    public function members(): HasMany
    {
        return $this->hasMany(SegmentMember::class, 'segment_id');
    }
}
