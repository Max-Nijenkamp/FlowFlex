<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'fin_bank_accounts';

    protected $fillable = ['company_id', 'name', 'bank_name', 'account_number', 'iban', 'iban_last4', 'currency', 'gl_account_id', 'current_balance_cents'];

    /** @var list<string> */
    protected $hidden = ['account_number', 'iban'];

    protected function casts(): array
    {
        return [
            'account_number' => 'encrypted',
            'iban' => 'encrypted',
            'current_balance_cents' => 'integer',
        ];
    }
}
