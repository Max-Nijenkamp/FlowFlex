<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'crm_contacts';

    protected $fillable = ['company_id', 'first_name', 'last_name', 'email', 'phone', 'job_title', 'account_id', 'lifecycle_stage', 'source', 'owner_id', 'custom_fields'];

    protected function casts(): array
    {
        return ['custom_fields' => 'array'];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
