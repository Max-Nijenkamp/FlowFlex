<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Crm\PipelineFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A named pipeline (crm.pipeline, ADR custom-pipelines). One default per
 * company; a pipeline with active stages cannot be deleted.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property bool $is_default
 * @property int $order
 */
class Pipeline extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<PipelineFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'crm_pipelines';

    protected $fillable = ['company_id', 'name', 'is_default', 'order'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_default' => 'boolean', 'order' => 'integer'];
    }

    /** @return HasMany<PipelineStage, $this> */
    public function stages(): HasMany
    {
        return $this->hasMany(PipelineStage::class, 'pipeline_id');
    }
}
