<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\States\CRM\Contract\ContractState;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Contract extends Model
{
    use BelongsToCompany, HasStates, HasUlids, SoftDeletes;

    protected $table = 'crm_contracts';

    protected $fillable = ['company_id', 'account_id', 'deal_id', 'title', 'value_cents', 'currency', 'billing_interval', 'start_date', 'end_date', 'renewal_date', 'auto_renew', 'notice_period_days', 'status', 'signed_at', 'signed_pdf_path', 'alerted_levels'];

    protected function casts(): array
    {
        return [
            'status' => ContractState::class,
            'value_cents' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'renewal_date' => 'date',
            'auto_renew' => 'boolean',
            'notice_period_days' => 'integer',
            'signed_at' => 'datetime',
            'alerted_levels' => 'array',
        ];
    }
}
