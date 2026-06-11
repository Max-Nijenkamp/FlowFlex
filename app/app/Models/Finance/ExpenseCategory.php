<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'fin_expense_categories';

    protected $fillable = ['company_id', 'name', 'limit_per_transaction_cents', 'gl_account_id'];

    protected function casts(): array
    {
        return ['limit_per_transaction_cents' => 'integer'];
    }
}
