<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Finance\CustomerFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Invoice recipient (finance.invoicing). Links to a CRM account when
 * the CRM module is active; standalone record otherwise.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string $email
 * @property array<string, mixed> $address
 * @property ?string $vat_number
 * @property ?string $crm_account_id
 * @property int $payment_terms_days
 */
class Customer extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'fin_customers';

    protected $fillable = [
        'company_id', 'name', 'email', 'address', 'vat_number',
        'crm_account_id', 'payment_terms_days',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['address' => 'array', 'payment_terms_days' => 'integer'];
    }

    /** @return HasMany<Invoice, $this> */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }
}
