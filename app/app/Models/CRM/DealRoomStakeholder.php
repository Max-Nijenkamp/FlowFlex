<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DealRoomStakeholder extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_deal_room_stakeholders';

    protected $fillable = ['company_id', 'room_id', 'name', 'role', 'contact_id'];
}
