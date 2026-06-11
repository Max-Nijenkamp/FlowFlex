<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DeiAttribute extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_dei_attributes';

    protected $fillable = ['company_id', 'employee_id', 'dimension', 'value', 'consented_at'];

    /** @var list<string> */
    protected $hidden = ['value'];

    protected function casts(): array
    {
        return ['value' => 'encrypted', 'consented_at' => 'datetime'];
    }
}
