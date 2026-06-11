<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    use BelongsToCompany, HasFactory, HasUlids;

    protected $table = 'fin_invoice_lines';

    protected $fillable = ['invoice_id', 'company_id', 'description', 'quantity', 'unit_price_cents', 'tax_cents', 'line_total_cents'];

    protected function casts(): array
    {
        return ['quantity' => 'float', 'unit_price_cents' => 'integer', 'tax_cents' => 'integer', 'line_total_cents' => 'integer'];
    }
}
