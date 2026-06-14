<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\LogsCompanyActivity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, LogsCompanyActivity, SoftDeletes;

    protected $table = 'fin_customers';

    protected $fillable = ['company_id', 'name', 'email', 'address', 'vat_number', 'crm_account_id', 'payment_terms_days', 'credit_limit_cents'];

    protected function casts(): array
    {
        return ['address' => 'array', 'payment_terms_days' => 'integer'];
    }
}
