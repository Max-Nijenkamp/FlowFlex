<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use BelongsToCompany, HasFactory, HasUlids;

    protected $table = 'fin_bank_transactions';

    protected $fillable = ['company_id', 'bank_account_id', 'transaction_date', 'description', 'amount_cents', 'import_hash', 'reconciled_at', 'journal_line_id'];

    protected function casts(): array
    {
        return ['transaction_date' => 'date', 'amount_cents' => 'integer', 'reconciled_at' => 'datetime'];
    }
}
