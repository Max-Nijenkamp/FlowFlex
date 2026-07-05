<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Crm\PipelineStageFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Board column (crm.pipeline). Owned here; crm_deals reference it. A
 * stage with deals cannot be deleted — reassign first.
 *
 * @property string $id
 * @property string $company_id
 * @property string $pipeline_id
 * @property string $name
 * @property int $order
 * @property numeric-string $probability_default
 * @property bool $is_won
 * @property bool $is_lost
 */
class PipelineStage extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<PipelineStageFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'crm_pipeline_stages';

    protected $fillable = [
        'company_id', 'pipeline_id', 'name', 'order',
        'probability_default', 'is_won', 'is_lost',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'probability_default' => 'decimal:2',
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
        ];
    }

    /** @return BelongsTo<Pipeline, $this> */
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class, 'pipeline_id');
    }

    /** @return HasMany<Deal, $this> */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'stage_id');
    }
}
