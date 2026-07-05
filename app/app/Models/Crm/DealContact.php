<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Deal ↔ contact link with a buying-committee role (crm.deals).
 *
 * @property string $id
 * @property string $company_id
 * @property string $deal_id
 * @property string $contact_id
 * @property ?string $role
 */
class DealContact extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'crm_deal_contacts';

    protected $fillable = ['company_id', 'deal_id', 'contact_id', 'role'];

    /** @return BelongsTo<Contact, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
