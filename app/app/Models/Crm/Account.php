<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\Crm\AccountFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Company/organisation record (crm.contacts). LTV updated only by the
 * InvoicePaid listener.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property ?string $industry
 * @property ?int $employee_count
 * @property ?string $website
 * @property ?string $phone
 * @property string $owner_id
 * @property int $lifetime_value_cents
 * @property array<string, mixed> $custom_fields
 */
class Account extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'crm_accounts';

    protected $fillable = [
        'company_id', 'name', 'industry', 'employee_count', 'website',
        'phone', 'owner_id', 'lifetime_value_cents', 'custom_fields',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'employee_count' => 'integer',
            'lifetime_value_cents' => 'integer',
            'custom_fields' => 'array',
        ];
    }

    /** @return HasMany<Contact, $this> */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'account_id');
    }

    /** @return HasMany<Deal, $this> */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'account_id');
    }

    /** @return BelongsTo<User, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
