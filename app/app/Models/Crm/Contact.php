<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\Crm\ContactFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Person record — the CRM anchor and platform-wide shared entity
 * (crm.contacts). "Lead" is not a model: lifecycle_stage = lead. The
 * stage column is a plain enum string — any move allowed, no state
 * machine (spec).
 *
 * @property string $id
 * @property string $company_id
 * @property string $first_name
 * @property string $last_name
 * @property ?string $email
 * @property ?string $phone
 * @property ?string $job_title
 * @property ?string $account_id
 * @property string $lifecycle_stage
 * @property ?string $source
 * @property string $owner_id
 * @property array<string, mixed> $custom_fields
 * @property-read string $full_name
 */
class Contact extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    public const LIFECYCLE_STAGES = [
        'lead', 'marketing_qualified', 'sales_qualified', 'opportunity', 'customer', 'churned',
    ];

    protected $table = 'crm_contacts';

    protected $fillable = [
        'company_id', 'first_name', 'last_name', 'email', 'phone', 'job_title',
        'account_id', 'lifecycle_stage', 'source', 'owner_id', 'custom_fields',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['custom_fields' => 'array'];
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /** @return BelongsTo<User, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** @return HasMany<ContactAccount, $this> */
    public function accountLinks(): HasMany
    {
        return $this->hasMany(ContactAccount::class, 'contact_id');
    }

    /** @return HasMany<Activity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'contact_id');
    }

    /** @return HasMany<Deal, $this> */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'contact_id');
    }
}
