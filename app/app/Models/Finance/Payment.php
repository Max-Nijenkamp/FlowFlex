<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use BelongsToCompany, HasFactory, HasUlids;

    protected $table = 'fin_payments';

    protected $fillable = ['company_id', 'invoice_id', 'amount_cents', 'payment_date', 'payment_method', 'reference_number'];

    protected function casts(): array
    {
        return ['amount_cents' => 'integer', 'payment_date' => 'date'];
    }
}
