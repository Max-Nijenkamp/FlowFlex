<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DeiSnapshot extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_dei_snapshots';

    protected $fillable = ['company_id', 'period', 'dimension', 'breakdown'];

    protected function casts(): array
    {
        return ['breakdown' => 'array'];
    }
}
