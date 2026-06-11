<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ArWriteoff extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'fin_ar_writeoffs';

    protected $fillable = ['company_id', 'invoice_id', 'amount_cents', 'reason', 'approved_by', 'written_off_at'];

    protected function casts(): array
    {
        return ['amount_cents' => 'integer', 'written_off_at' => 'datetime'];
    }
}
