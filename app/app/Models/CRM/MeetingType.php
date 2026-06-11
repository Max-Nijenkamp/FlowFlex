<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingType extends Model
{
    use BelongsToCompany, HasUlids, SoftDeletes;

    protected $table = 'crm_meeting_types';

    protected $fillable = ['company_id', 'owner_id', 'name', 'slug', 'duration_minutes', 'location_type', 'video_link', 'buffer_minutes', 'price_cents', 'team_user_ids'];

    protected function casts(): array
    {
        return ['duration_minutes' => 'integer', 'buffer_minutes' => 'integer', 'price_cents' => 'integer', 'team_user_ids' => 'array'];
    }
}
