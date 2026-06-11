<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class WinLoss extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_win_loss';

    protected $fillable = ['company_id', 'deal_id', 'outcome', 'reason', 'competitor', 'notes'];
}
