<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A sales pipeline (crm.pipeline). Companies can run several — each with its
 * own custom stages (Pipedrive pattern, founder request 2026-06-12).
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property bool $is_default
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Pipeline extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'crm_pipelines';

    protected $fillable = ['company_id', 'name', 'is_default', 'order'];

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
