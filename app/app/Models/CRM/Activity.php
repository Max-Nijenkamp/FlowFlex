<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'crm_activities';

    protected $fillable = ['company_id', 'type', 'subject', 'body', 'contact_id', 'deal_id', 'owner_id', 'due_at', 'completed_at'];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}
