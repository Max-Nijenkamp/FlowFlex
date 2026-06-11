<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\States\CRM\Deal\DealState;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

/**
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string|null $account_id
 * @property string|null $contact_id
 * @property string $owner_id
 * @property string $stage_id
 * @property int $value_cents
 * @property string $currency
 * @property float $probability
 * @property DealState $status
 * @property string|null $lost_reason
 * @property-read Contact|null $contact
 * @property-read PipelineStage $stage
 */
class Deal extends Model
{
    use BelongsToCompany, HasFactory, HasStates, HasUlids, SoftDeletes;

    protected $table = 'crm_deals';

    protected $fillable = [
        'company_id', 'name', 'account_id', 'contact_id', 'owner_id', 'stage_id',
        'value_cents', 'currency', 'probability', 'expected_close_date',
        'actual_close_date', 'status', 'lost_reason', 'stage_entered_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => DealState::class,
            'value_cents' => 'integer',
            'probability' => 'float',
            'expected_close_date' => 'date',
            'actual_close_date' => 'date',
            'stage_entered_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Contact, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /** @return BelongsTo<PipelineStage, $this> */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'stage_id');
    }
}
