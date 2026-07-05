<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Models\User;
use App\States\Crm\Deal\DealState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\Crm\DealFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\ModelStates\HasStates;

/**
 * Deal — the core revenue object (crm.deals). All money integer cents
 * via brick/money; status is a spatie state machine (open → won|lost);
 * closed deals are immutable through DealService.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property ?string $account_id
 * @property ?string $contact_id
 * @property string $owner_id
 * @property string $stage_id
 * @property int $value_cents
 * @property string $currency
 * @property numeric-string $probability
 * @property ?Carbon $expected_close_date
 * @property ?Carbon $actual_close_date
 * @property DealState $status
 * @property ?string $lost_reason
 * @property ?string $lost_to
 * @property Carbon $stage_entered_at
 */
class Deal extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<DealFactory> */
    use HasFactory;

    use HasStates;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'crm_deals';

    protected $fillable = [
        'company_id', 'name', 'account_id', 'contact_id', 'owner_id', 'stage_id',
        'value_cents', 'currency', 'probability', 'expected_close_date',
        'actual_close_date', 'status', 'lost_reason', 'lost_to', 'stage_entered_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'value_cents' => 'integer',
            'probability' => 'decimal:2',
            'expected_close_date' => 'date',
            'actual_close_date' => 'date',
            'status' => DealState::class,
            'stage_entered_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<PipelineStage, $this> */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'stage_id');
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /** @return BelongsTo<Contact, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /** @return BelongsTo<User, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** @return HasMany<DealContact, $this> */
    public function dealContacts(): HasMany
    {
        return $this->hasMany(DealContact::class, 'deal_id');
    }

    /** @return HasMany<DealProduct, $this> */
    public function products(): HasMany
    {
        return $this->hasMany(DealProduct::class, 'deal_id');
    }

    /** @return HasMany<Activity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'deal_id');
    }

    public function isClosed(): bool
    {
        return (string) $this->status !== 'open';
    }
}
