<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string $calculation_type percent|flat
 * @property int $value basis points (percent) or cents (flat)
 * @property bool $is_employer_contribution
 */
class DeductionType extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'hr_deduction_types';

    protected $fillable = ['company_id', 'name', 'calculation_type', 'value', 'is_employer_contribution'];

    protected function casts(): array
    {
        return ['value' => 'integer', 'is_employer_contribution' => 'boolean'];
    }
}
