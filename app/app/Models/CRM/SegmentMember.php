<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class SegmentMember extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_segment_members';

    protected $fillable = ['company_id', 'segment_id', 'contact_id'];
}
