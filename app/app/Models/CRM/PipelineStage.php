<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PipelineStage extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'crm_pipeline_stages';

    protected $fillable = ['company_id', 'name', 'order', 'probability_default'];

    protected function casts(): array
    {
        return ['order' => 'integer', 'probability_default' => 'float'];
    }
}
