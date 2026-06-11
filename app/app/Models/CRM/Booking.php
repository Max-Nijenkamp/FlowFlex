<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_bookings';

    protected $fillable = ['company_id', 'meeting_type_id', 'contact_id', 'assigned_rep_id', 'scheduled_at', 'status', 'stripe_payment_intent_id', 'reminded_at'];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'reminded_at' => 'datetime'];
    }

    /** @return BelongsTo<MeetingType, $this> */
    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class, 'meeting_type_id');
    }
}
