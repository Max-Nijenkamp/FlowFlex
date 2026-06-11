<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'fin_suppliers';

    protected $fillable = ['company_id', 'name', 'email', 'vat_number', 'iban', 'iban_last4', 'payment_terms_days'];

    protected function casts(): array
    {
        return ['iban' => 'encrypted', 'payment_terms_days' => 'integer'];
    }

    protected static function booted(): void
    {
        static::saving(function (Supplier $supplier): void {
            if ($supplier->isDirty('iban')) {
                $supplier->iban_last4 = $supplier->iban !== null ? substr($supplier->iban, -4) : null;
            }
        });
    }
}
