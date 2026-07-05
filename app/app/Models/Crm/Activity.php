<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\Crm\ActivityFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * CRM activity — call/email/meeting/task/note on a contact, deal or
 * account (crm.activities). Not to be confused with App\Models\Activity,
 * the platform audit row. Tasks carry due_at + is_complete; reminded_at
 * is the reminder-once guard.
 *
 * @property string $id
 * @property string $company_id
 * @property string $type
 * @property string $subject
 * @property ?string $description
 * @property string $owner_id
 * @property ?string $contact_id
 * @property ?string $deal_id
 * @property ?string $account_id
 * @property Carbon $activity_date
 * @property ?int $duration_minutes
 * @property ?string $outcome
 * @property bool $is_complete
 * @property ?Carbon $due_at
 * @property ?Carbon $reminded_at
 */
class Activity extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<ActivityFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    public const TYPES = ['call', 'email', 'meeting', 'task', 'note'];

    protected $table = 'crm_activities';

    protected $fillable = [
        'company_id', 'type', 'subject', 'description', 'owner_id',
        'contact_id', 'deal_id', 'account_id', 'activity_date',
        'duration_minutes', 'outcome', 'is_complete', 'due_at', 'reminded_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'activity_date' => 'datetime',
            'duration_minutes' => 'integer',
            'is_complete' => 'boolean',
            'due_at' => 'datetime',
            'reminded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** @return BelongsTo<Contact, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /** @return BelongsTo<Deal, $this> */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function isOverdue(): bool
    {
        return ! $this->is_complete
            && $this->due_at !== null
            && $this->due_at->isPast();
    }
}
