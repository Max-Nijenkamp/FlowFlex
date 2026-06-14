<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\LogsCompanyActivity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Account extends Model implements HasMedia
{
    use BelongsToCompany, HasFactory, HasUlids, InteractsWithMedia, LogsCompanyActivity, SoftDeletes;

    protected $table = 'crm_accounts';

    protected $fillable = ['company_id', 'name', 'industry', 'employee_count', 'website', 'phone', 'owner_id', 'lifetime_value_cents', 'custom_fields'];

    protected function casts(): array
    {
        return ['custom_fields' => 'array', 'lifetime_value_cents' => 'integer', 'employee_count' => 'integer'];
    }

    /** @return HasMany<Contact, $this> */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'account_id');
    }

    /** @return HasMany<Deal, $this> */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'account_id');
    }
}
