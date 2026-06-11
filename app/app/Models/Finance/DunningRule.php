<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DunningRule extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'fin_ar_dunning_rules';

    protected $fillable = ['company_id', 'aging_bucket', 'days_overdue', 'email_template', 'escalation_level', 'is_active'];

    protected function casts(): array
    {
        return ['days_overdue' => 'integer', 'escalation_level' => 'integer', 'is_active' => 'boolean'];
    }
}
