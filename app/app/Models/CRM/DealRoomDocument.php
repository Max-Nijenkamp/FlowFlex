<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DealRoomDocument extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_deal_room_documents';

    protected $fillable = ['company_id', 'room_id', 'name', 'path', 'view_count', 'last_viewed_at'];

    protected function casts(): array
    {
        return ['view_count' => 'integer', 'last_viewed_at' => 'datetime'];
    }
}
