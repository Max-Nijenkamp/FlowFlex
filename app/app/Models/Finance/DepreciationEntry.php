<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DepreciationEntry extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_depreciation_entries';

    protected $fillable = ['company_id', 'asset_id', 'period', 'depreciation_cents', 'journal_entry_id'];

    protected function casts(): array
    {
        return ['depreciation_cents' => 'integer'];
    }
}
