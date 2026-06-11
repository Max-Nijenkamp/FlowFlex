<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DealRoomActionItem extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_deal_room_action_items';

    protected $fillable = ['company_id', 'room_id', 'description', 'owner_side', 'status', 'due_date'];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }
}
