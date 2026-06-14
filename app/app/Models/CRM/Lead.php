<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\LogsCompanyActivity;
use Database\Factories\CRM\LeadFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Inbound/prospect record before it becomes a qualified Deal. Every CRM has a
 * top-of-funnel; leads capture it and convert into the pipeline (founder
 * request 2026-06-14). *(assumed)* spec — no vault module yet.
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string|null $company_name
 * @property string|null $email
 * @property string|null $phone
 * @property string $source
 * @property string $status
 * @property string|null $owner_id
 * @property int $estimated_value_cents
 * @property string|null $notes
 * @property string|null $converted_deal_id
 * @property Carbon|null $converted_at
 */
class Lead extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, LogsCompanyActivity, SoftDeletes;

    protected $table = 'crm_leads';

    protected $fillable = [
        'company_id', 'name', 'company_name', 'email', 'phone', 'source',
        'status', 'owner_id', 'estimated_value_cents', 'notes',
        'converted_deal_id', 'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value_cents' => 'integer',
            'converted_at' => 'datetime',
        ];
    }

    public function isConverted(): bool
    {
        return $this->converted_deal_id !== null;
    }

    /** @return BelongsTo<User, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** @return BelongsTo<Deal, $this> */
    public function convertedDeal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'converted_deal_id');
    }

    protected static function newFactory(): LeadFactory
    {
        return LeadFactory::new();
    }
}
