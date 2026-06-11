<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DealRoom extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_deal_rooms';

    protected $fillable = ['company_id', 'deal_id', 'access_token', 'branding', 'expires_at', 'revoked_at'];

    protected function casts(): array
    {
        return ['branding' => 'array', 'expires_at' => 'datetime', 'revoked_at' => 'datetime'];
    }

    public function isLive(): bool
    {
        return $this->revoked_at === null && $this->expires_at->isFuture();
    }

    /** @return HasMany<DealRoomDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(DealRoomDocument::class, 'room_id');
    }

    /** @return HasMany<DealRoomActionItem, $this> */
    public function actionItems(): HasMany
    {
        return $this->hasMany(DealRoomActionItem::class, 'room_id');
    }

    /** @return HasMany<DealRoomStakeholder, $this> */
    public function stakeholders(): HasMany
    {
        return $this->hasMany(DealRoomStakeholder::class, 'room_id');
    }
}
