<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Benefit extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'hr_benefits';

    protected $fillable = ['company_id', 'name', 'type', 'cost_per_month_cents', 'employer_contribution_cents'];

    protected function casts(): array
    {
        return ['cost_per_month_cents' => 'integer', 'employer_contribution_cents' => 'integer'];
    }
}
