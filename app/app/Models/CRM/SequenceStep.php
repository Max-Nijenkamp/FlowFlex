<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class SequenceStep extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_sequence_steps';

    protected $fillable = ['company_id', 'sequence_id', 'order', 'type', 'config', 'wait_days'];

    protected function casts(): array
    {
        return ['order' => 'integer', 'config' => 'array', 'wait_days' => 'integer'];
    }
}
