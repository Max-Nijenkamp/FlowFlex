<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Contact ↔ account link with a per-company role title (crm.contacts —
 * a contact can belong to several companies).
 *
 * @property string $id
 * @property string $company_id
 * @property string $contact_id
 * @property string $account_id
 * @property ?string $title
 * @property bool $is_primary
 */
class ContactAccount extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'crm_contact_accounts';

    protected $fillable = ['company_id', 'contact_id', 'account_id', 'title', 'is_primary'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    /** @return BelongsTo<Contact, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
